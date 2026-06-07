<?php

namespace App\Console\Commands;

use App\Models\Lider;
use App\Models\Role;
use App\Models\User;
use App\Models\Votante;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

class ImportPunteros extends Command
{
    private const RESERVE_EMAIL = 'votos-seguros@punteros.local';

    protected $signature = 'punteros:importar
        {archivo=punteros.xlsx : Ruta del archivo XLSX}
        {--dry-run : Analizar el archivo sin modificar la base de datos}';

    protected $description = 'Importar lideres y votantes desde un libro con una pestana por lider';

    public function handle(): int
    {
        $path = $this->resolvePath((string) $this->argument('archivo'));

        if (! is_file($path)) {
            $this->error("No se encontro el archivo: {$path}");

            return self::FAILURE;
        }

        try {
            $data = $this->readWorkbook($path);
        } catch (Throwable $e) {
            $this->error('No se pudo leer el archivo: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->showFileSummary($data);

        if ($this->option('dry-run')) {
            $this->showDryRunSummary($data);

            return self::SUCCESS;
        }

        try {
            $result = DB::transaction(fn () => $this->import($data));
        } catch (Throwable $e) {
            $this->error('La importacion fue revertida: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Importacion completada.');
        $this->table(
            ['Concepto', 'Cantidad'],
            [
                ['Lideres creados', $result['leaders_created']],
                ['Lideres existentes', $result['leaders_existing']],
                ['Votantes creados', $result['voters_created']],
                ['Votantes actualizados', $result['voters_updated']],
                ['Votantes restaurados', $result['voters_restored']],
            ]
        );

        return self::SUCCESS;
    }

    private function resolvePath(string $path): string
    {
        if (preg_match('/^(?:[A-Za-z]:[\\\\\/]|[\\\\\/]{2})/', $path) === 1) {
            return $path;
        }

        return base_path($path);
    }

    /**
     * @return array{
     *     leaders: array<string, array{name: string, voters: array<string, array<string, mixed>>}>,
     *     duplicate_rows: int,
     *     conflicts: array<int, array<string, string>>,
     *     missing_ci: array<int, array<string, string|int>>,
     *     skipped_sheets: array<int, string>
     * }
     */
    private function readWorkbook(string $path): array
    {
        $spreadsheet = IOFactory::load($path);
        $leaders = [];
        $seenVoters = [];
        $duplicateRows = 0;
        $conflicts = [];
        $missingCi = [];
        $skippedSheets = [];
        $worksheets = iterator_to_array($spreadsheet->getWorksheetIterator());

        usort($worksheets, function ($left, $right) {
            return (int) $this->isReserveSheet($right->getTitle())
                <=> (int) $this->isReserveSheet($left->getTitle());
        });

        foreach ($worksheets as $sheet) {
            $columns = $this->detectColumns($sheet);

            if ($columns === null) {
                $skippedSheets[] = $sheet->getTitle();

                continue;
            }

            $isReserve = $this->isReserveSheet($sheet->getTitle());
            $leaderName = $isReserve
                ? 'VOTOS SEGUROS'
                : $this->cleanText($sheet->getTitle());
            $leaderKey = $isReserve
                ? '__votos_seguros__'
                : $this->normalizeLeaderKey($leaderName);

            if ($leaderKey === '') {
                continue;
            }

            $leaders[$leaderKey] ??= [
                'name' => $leaderName,
                'is_reserve' => $isReserve,
                'voters' => [],
            ];

            for ($row = $columns['header_row'] + 1; $row <= $sheet->getHighestDataRow(); $row++) {
                $ci = $this->normalizeCi(
                    (string) $sheet->getCell([$columns['ci'], $row])->getFormattedValue()
                );
                $lastNames = $this->cleanText(
                    (string) $sheet->getCell([$columns['last_names'], $row])->getFormattedValue()
                );
                $names = $this->cleanText(
                    (string) $sheet->getCell([$columns['names'], $row])->getFormattedValue()
                );
                $table = isset($columns['table'])
                    ? $this->normalizeTable(
                        (string) $sheet->getCell([$columns['table'], $row])->getFormattedValue()
                    )
                    : null;

                if ($ci === '' && $lastNames === '' && $names === '') {
                    continue;
                }

                if ($ci === '') {
                    $missingCi[] = [
                        'sheet' => $sheet->getTitle(),
                        'row' => $row,
                        'name' => trim("{$names} {$lastNames}"),
                    ];

                    continue;
                }

                if (isset($seenVoters[$ci])) {
                    $duplicateRows++;
                    $previous = $seenVoters[$ci];

                    if ($previous['leader_key'] !== $leaderKey) {
                        $conflicts[] = [
                            'ci' => $ci,
                            'kept_leader' => $previous['leader_name'],
                            'ignored_leader' => $leaderName,
                        ];
                    }

                    continue;
                }

                $voter = [
                    'ci' => $ci,
                    'nombres' => $names,
                    'apellidos' => $lastNames,
                    'mesa' => $table,
                ];

                $leaders[$leaderKey]['voters'][$ci] = $voter;
                $seenVoters[$ci] = [
                    'leader_key' => $leaderKey,
                    'leader_name' => $leaderName,
                ];
            }
        }

        return [
            'leaders' => $leaders,
            'duplicate_rows' => $duplicateRows,
            'conflicts' => $conflicts,
            'missing_ci' => $missingCi,
            'skipped_sheets' => $skippedSheets,
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, int>
     */
    private function import(array $data): array
    {
        $leaderRole = Role::where('slug', 'lider')->firstOrFail();
        $actorId = User::whereHas('role', fn ($query) => $query
            ->whereIn('slug', ['superadmin', 'admin']))
            ->value('id');
        $existingLeaderUsers = User::where('role_id', $leaderRole->id)
            ->with('lider')
            ->get()
            ->keyBy(fn (User $user) => $this->normalizeLeaderKey($user->name));

        $result = [
            'leaders_created' => 0,
            'leaders_existing' => 0,
            'voters_created' => 0,
            'voters_updated' => 0,
            'voters_restored' => 0,
        ];

        foreach ($data['leaders'] as $leaderKey => $leaderData) {
            if ($leaderData['is_reserve']) {
                $user = User::firstOrCreate(
                    ['email' => self::RESERVE_EMAIL],
                    [
                        'name' => 'VOTOS SEGUROS',
                        'password' => Str::random(40),
                        'role_id' => $leaderRole->id,
                        'activo' => true,
                        'email_verified_at' => now(),
                    ]
                );
                $user->load('lider');

                if ($user->wasRecentlyCreated) {
                    $result['leaders_created']++;
                }
            } else {
                /** @var User|null $user */
                $user = $existingLeaderUsers->get($leaderKey);
            }

            if (! $user) {
                $user = User::create([
                    'name' => $leaderData['name'],
                    'email' => $this->uniqueLeaderEmail($leaderData['name']),
                    'password' => Str::random(40),
                    'role_id' => $leaderRole->id,
                    'activo' => true,
                    'email_verified_at' => now(),
                ]);
                $result['leaders_created']++;
            } elseif (! $leaderData['is_reserve']) {
                $result['leaders_existing']++;
            }

            $leader = $user->lider;

            if (! $leader) {
                $leader = Lider::create([
                    'usuario_id' => $user->id,
                    'territorio' => $leaderData['is_reserve']
                        ? 'Votos seguros pendientes de asignacion'
                        : 'Puntero: '.$leaderData['name'],
                    'meta_votos' => count($leaderData['voters']),
                    'activo' => true,
                ]);
            }

            foreach ($leaderData['voters'] as $voterData) {
                $voter = Votante::withTrashed()->where('ci', $voterData['ci'])->first();
                $attributes = [
                    'nombres' => $voterData['nombres'],
                    'apellidos' => $voterData['apellidos'],
                    'lider_asignado_id' => $leader->id,
                    'actualizado_por_usuario_id' => $actorId,
                ];

                if ($voterData['mesa'] !== null) {
                    $attributes['mesa'] = $voterData['mesa'];
                }

                if ($voter) {
                    if ($voter->trashed()) {
                        $voter->restore();
                        $result['voters_restored']++;
                    }

                    $voter->update($attributes);
                    $result['voters_updated']++;
                } else {
                    Votante::create($attributes + [
                        'ci' => $voterData['ci'],
                        'creado_por_usuario_id' => $actorId,
                    ]);
                    $result['voters_created']++;
                }
            }
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function showFileSummary(array $data): void
    {
        $voterCount = array_sum(array_map(
            fn (array $leader) => count($leader['voters']),
            $data['leaders']
        ));
        $reserveCount = collect($data['leaders'])
            ->firstWhere('is_reserve', true)['voters'] ?? [];

        $this->table(
            ['Dato del archivo', 'Cantidad'],
            [
                ['Lideres unicos', count($data['leaders'])],
                ['Votantes con cedula unica', $voterCount],
                ['Votos seguros', count($reserveCount)],
                ['Votantes asignados a lideres', $voterCount - count($reserveCount)],
                ['Filas duplicadas', $data['duplicate_rows']],
                ['Conflictos entre lideres', count($data['conflicts'])],
                ['Filas omitidas sin cedula', count($data['missing_ci'])],
                ['Hojas no electorales omitidas', count($data['skipped_sheets'])],
            ]
        );

        foreach ($data['conflicts'] as $conflict) {
            $this->warn(
                "CI {$conflict['ci']}: se conserva {$conflict['kept_leader']} "
                ."y se omite la repeticion en {$conflict['ignored_leader']}."
            );
        }

        foreach ($data['missing_ci'] as $missing) {
            $this->warn(
                "Sin cedula: {$missing['sheet']}, fila {$missing['row']} ({$missing['name']})."
            );
        }

        foreach ($data['skipped_sheets'] as $sheet) {
            $this->warn("Hoja omitida por no tener formato de votantes: {$sheet}.");
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function showDryRunSummary(array $data): void
    {
        $leaderRole = Role::where('slug', 'lider')->first();
        $existingNames = $leaderRole
            ? User::where('role_id', $leaderRole->id)->pluck('name')
                ->map(fn (string $name) => $this->normalizeLeaderKey($name))
                ->flip()
            : collect();
        $cis = collect($data['leaders'])
            ->flatMap(fn (array $leader) => array_keys($leader['voters']))
            ->values();
        $existingVoters = Votante::withTrashed()->whereIn('ci', $cis)->count();
        $regularLeaders = collect($data['leaders'])
            ->reject(fn (array $leader) => $leader['is_reserve']);
        $hasReserveSheet = collect($data['leaders'])
            ->contains(fn (array $leader) => $leader['is_reserve']);
        $reserveExists = User::where('email', self::RESERVE_EMAIL)->exists();
        $existingLeaders = $regularLeaders
            ->keys()
            ->filter(fn (string $key) => $existingNames->has($key))
            ->count();
        $leadersToCreate = $regularLeaders->count() - $existingLeaders
            + ($hasReserveSheet && ! $reserveExists ? 1 : 0);
        $leadersToReuse = $existingLeaders
            + ($hasReserveSheet && $reserveExists ? 1 : 0);

        $this->newLine();
        $this->info('Simulacion: no se modifico la base de datos.');
        $this->table(
            ['Accion prevista', 'Cantidad'],
            [
                ['Crear lideres', $leadersToCreate],
                ['Reutilizar lideres', $leadersToReuse],
                ['Crear votantes', $cis->count() - $existingVoters],
                ['Actualizar/reasignar votantes', $existingVoters],
            ]
        );
    }

    private function uniqueLeaderEmail(string $name): string
    {
        $slug = Str::slug(Str::ascii($name)) ?: 'lider';
        $email = "{$slug}@punteros.local";
        $suffix = 2;

        while (User::where('email', $email)->exists()) {
            $email = "{$slug}-{$suffix}@punteros.local";
            $suffix++;
        }

        return $email;
    }

    private function normalizeName(string $value): string
    {
        return Str::lower(Str::ascii($this->cleanText($value)));
    }

    private function normalizeLeaderKey(string $value): string
    {
        $key = $this->normalizeName($value);

        return match ($key) {
            'isa meza' => 'ysa meza',
            default => $key,
        };
    }

    private function isReserveSheet(string $title): bool
    {
        return $this->normalizeName($title) === 'votos seguros';
    }

    /**
     * @return array{header_row: int, ci: int, last_names: int, names: int, table?: int}|null
     */
    private function detectColumns($sheet): ?array
    {
        $maxRow = min(10, $sheet->getHighestDataRow());
        $maxColumn = min(
            10,
            \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString(
                $sheet->getHighestDataColumn()
            )
        );

        for ($row = 1; $row <= $maxRow; $row++) {
            $columns = ['header_row' => $row];

            for ($column = 1; $column <= $maxColumn; $column++) {
                $header = $this->normalizeName(
                    (string) $sheet->getCell([$column, $row])->getFormattedValue()
                );

                if (in_array($header, ['cedula', 'ci', 'numero ced', 'numero cedula'], true)) {
                    $columns['ci'] = $column;
                } elseif (in_array($header, ['apellido', 'apellidos'], true)) {
                    $columns['last_names'] = $column;
                } elseif (in_array($header, ['nombre', 'nombres'], true)) {
                    $columns['names'] = $column;
                } elseif (str_contains($header, 'mesa')) {
                    $columns['table'] = $column;
                }
            }

            if (isset($columns['ci'], $columns['last_names'], $columns['names'])) {
                return $columns;
            }
        }

        return null;
    }

    private function normalizeCi(string $value): string
    {
        $digits = preg_replace('/\D+/', '', $value) ?? '';

        return ltrim($digits, '0') ?: ($digits === '' ? '' : '0');
    }

    private function normalizeTable(string $value): ?string
    {
        $value = $this->cleanText($value);

        return preg_match('/^\d{1,10}$/', $value) === 1
            ? $value
            : null;
    }

    private function cleanText(string $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', $value) ?? '');
    }
}
