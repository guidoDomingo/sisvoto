<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Votante;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportVotosSegurosAdicionales extends Command
{
    protected $signature = 'votos-seguros:importar-adicionales
        {--dry-run : Mostrar los cambios sin modificar la base de datos}';

    protected $description = 'Importar la lista adicional de votantes seguros sin duplicar cedulas';

    public function handle(): int
    {
        $leader = User::where('email', 'votos-seguros@punteros.local')
            ->with('lider')
            ->first();

        if (! $leader?->lider) {
            $this->error('No existe el lider VOTOS SEGUROS. Importe primero el archivo Excel.');

            return self::FAILURE;
        }

        $people = $this->people();
        $existing = Votante::withTrashed()
            ->whereIn('ci', array_column($people, 'ci'))
            ->get()
            ->keyBy('ci');
        $toCreate = collect($people)->whereNotIn('ci', $existing->keys())->count();
        $toReassign = count($people) - $toCreate;

        if ($this->option('dry-run')) {
            $this->info('Simulacion: no se modifico la base de datos.');
            $this->showSummary($toCreate, $toReassign, $leader->lider->votantes()->count());

            return self::SUCCESS;
        }

        $actorId = User::whereHas('role', fn ($query) => $query
            ->whereIn('slug', ['superadmin', 'admin']))
            ->value('id');

        $result = DB::transaction(function () use ($people, $leader, $actorId) {
            $created = 0;
            $reassigned = 0;

            foreach ($people as $person) {
                $voter = Votante::withTrashed()->where('ci', $person['ci'])->first();

                if ($voter) {
                    if ($voter->trashed()) {
                        $voter->restore();
                    }

                    $voter->update([
                        'lider_asignado_id' => $leader->lider->id,
                        'actualizado_por_usuario_id' => $actorId,
                    ]);
                    $reassigned++;

                    continue;
                }

                Votante::create($person + [
                    'lider_asignado_id' => $leader->lider->id,
                    'creado_por_usuario_id' => $actorId,
                    'actualizado_por_usuario_id' => $actorId,
                ]);
                $created++;
            }

            return compact('created', 'reassigned');
        });

        $this->info('Votos seguros adicionales importados.');
        $this->showSummary(
            $result['created'],
            $result['reassigned'],
            $leader->lider->votantes()->count()
        );

        return self::SUCCESS;
    }

    private function showSummary(int $created, int $reassigned, int $total): void
    {
        $this->table(
            ['Concepto', 'Cantidad'],
            [
                ['Votantes nuevos', $created],
                ['Votantes existentes reasignados', $reassigned],
                ['Total actual en VOTOS SEGUROS', $total],
            ]
        );
    }

    /**
     * @return array<int, array{ci: string, nombres: string, apellidos: string}>
     */
    private function people(): array
    {
        return [
            ['ci' => '6166902', 'nombres' => 'BRUNO', 'apellidos' => 'QUINTANA'],
            ['ci' => '5698359', 'nombres' => 'HECTOR', 'apellidos' => 'CACERES'],
            ['ci' => '2129869', 'nombres' => 'LIDA', 'apellidos' => 'CHENA'],
            ['ci' => '3969989', 'nombres' => 'HECTOR', 'apellidos' => 'CACERES'],
            ['ci' => '5670696', 'nombres' => 'JOSE', 'apellidos' => 'MARTINEZ'],
            ['ci' => '7783676', 'nombres' => 'FIDEL', 'apellidos' => 'GONZALEZ'],
            ['ci' => '660235', 'nombres' => 'JORGELINA', 'apellidos' => 'CHENA'],
            ['ci' => '6036007', 'nombres' => 'FERNANDO', 'apellidos' => 'CHENA'],
            ['ci' => '5837197', 'nombres' => 'THIAGO ALEJANDRO', 'apellidos' => 'CACERES GONZALEZ'],
            ['ci' => '5503480', 'nombres' => 'FATIMA', 'apellidos' => 'MEZA TELLEZ'],
            ['ci' => '2106514', 'nombres' => 'MIRTHA', 'apellidos' => 'TELLEZ FERREIRA'],
            ['ci' => '1463283', 'nombres' => 'EDGAR MANUEL', 'apellidos' => 'MEZA BOGADO'],
            ['ci' => '1749689', 'nombres' => 'MIRTA CONCEPCION', 'apellidos' => 'SANCHEZ DE CHAPARRO'],
            ['ci' => '6669569', 'nombres' => 'ROSALIA', 'apellidos' => 'PACUA'],
            ['ci' => '5260950', 'nombres' => 'CARMEN SOLEDAD', 'apellidos' => 'VALLEJOS RUIZ DIAZ'],
            ['ci' => '5294113', 'nombres' => 'MARIA MAGDALENA', 'apellidos' => 'MENDEZ ARAUJO'],
            ['ci' => '5848271', 'nombres' => 'RITA HERMELINDA', 'apellidos' => 'CANIZA ROMERO'],
            ['ci' => '4450692', 'nombres' => 'EMILIA', 'apellidos' => 'ESTIGARRIBIA MALDONADO'],
            ['ci' => '2572378', 'nombres' => 'CATALINA', 'apellidos' => 'MOREL'],
            ['ci' => '5528118', 'nombres' => 'BIANCA NOEMI', 'apellidos' => 'GOMEZ'],
            ['ci' => '4681385', 'nombres' => 'CAROLINA', 'apellidos' => 'BENITEZ SANTA CRUZ'],
            ['ci' => '6165206', 'nombres' => 'DIANA MABEL', 'apellidos' => 'DIAZ'],
            ['ci' => '4048308', 'nombres' => 'ROSAURA MARLENE', 'apellidos' => 'RAMIREZ GONZALEZ'],
            ['ci' => '6044561', 'nombres' => 'PAULA CELESTE', 'apellidos' => 'ARIAS CHENA'],
            ['ci' => '4450728', 'nombres' => 'MARIANELA SOLEDAD', 'apellidos' => 'CHAPARRO'],
            ['ci' => '6155762', 'nombres' => 'CHISSELA CAROLINA', 'apellidos' => 'CHENA SEGOVIA'],
            ['ci' => '2457865', 'nombres' => 'SONYU PAOLA', 'apellidos' => 'GODOY'],
            ['ci' => '5540959', 'nombres' => 'LEILA ROMINA', 'apellidos' => 'MEZA ARANDA'],
            ['ci' => '2839216', 'nombres' => 'ISIDORO', 'apellidos' => 'NAMANDU'],
            ['ci' => '7231950', 'nombres' => 'ROSA PABLINA', 'apellidos' => 'ACOSTA'],
            ['ci' => '4450706', 'nombres' => 'LIZA CONCEPCION', 'apellidos' => 'GOMEZ CHENA'],
            ['ci' => '6165208', 'nombres' => 'MIRTHA ELIZABETH', 'apellidos' => 'DIAZ'],
        ];
    }
}
