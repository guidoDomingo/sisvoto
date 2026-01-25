<div>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Gestión de Votantes</h1>
        <p class="mt-1 text-sm text-gray-600">Administra y consulta la base de datos de votantes</p>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div role="alert" class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded relative">
            {{ session('message') }}
        </div>
    @endif

    <!-- Filters and Actions -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por nombre, CI, teléfono..." 
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <!-- Intención -->
            <select wire:model.live="filtroIntencion" class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Todas las intenciones</option>
                <option value="A">A - Seguro</option>
                <option value="B">B - Probable</option>
                <option value="C">C - Indeciso</option>
                <option value="D">D - Difícil</option>
                <option value="E">E - Contrario</option>
            </select>

            <!-- Estado -->
            <select wire:model.live="filtroEstado" class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Todos los estados</option>
                <option value="Nuevo">Nuevo</option>
                <option value="Contactado">Contactado</option>
                <option value="Re-contacto">Re-contacto</option>
                <option value="Comprometido">Comprometido</option>
                <option value="Crítico">Crítico</option>
            </select>

            <!-- Transporte -->
            <select wire:model.live="filtroTransporte" class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Todos</option>
                <option value="1">Necesita transporte</option>
                <option value="0">No necesita</option>
            </select>
        </div>

        <!-- Segunda fila de filtros para datos TSJE -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <!-- Distrito -->
            <select wire:model.live="filtroDistrito" class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Todos los distritos</option>
                @foreach($distritos as $distrito)
                    <option value="{{ $distrito }}">{{ $distrito }}</option>
                @endforeach
            </select>

            <!-- Líder -->
            <select wire:model.live="filtroLider" class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Todos los líderes</option>
                @foreach($lideres as $lider)
                    <option value="{{ $lider->id }}">{{ $lider->usuario->name }}</option>
                @endforeach
            </select>

            <!-- Espacio para futuro filtro -->
            <div></div>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('votantes.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                </svg>
                Nuevo Votante
            </a>
            
            <button wire:click="limpiarFiltros" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                </svg>
                Limpiar
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th wire:click="sortBy('ci')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            CI
                        </th>
                        <th wire:click="sortBy('nombres')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            Nombre
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Teléfono
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Dirección
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Mesa/Orden
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Distrito
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Líder
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Intención
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($votantes as $votante)
                        <tr class="hover:bg-gray-50 {{ $votante->ya_voto ? 'bg-green-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $votante->ci }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $votante->nombres }} {{ $votante->apellidos }}</div>
                                <div class="flex items-center gap-1 mt-1">
                                    @if($votante->necesita_transporte)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                                            </svg>
                                            Transporte
                                        </span>
                                    @endif
                                    @if($votante->nro_registro)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800" 
                                              title="Número de registro TSJE: {{ $votante->nro_registro }}">
                                            TSJE
                                        </span>
                                    @endif
                                </div>
                                @if($votante->local_votacion)
                                    <div class="text-xs text-gray-500 mt-1" title="{{ $votante->descripcion_local }}">
                                        {{ Str::limit($votante->local_votacion, 25) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $votante->telefono ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <div class="max-w-xs truncate" title="{{ $votante->direccion }}">
                                    {{ $votante->direccion ?? '-' }}
                                </div>
                                @if($votante->barrio)
                                    <div class="text-xs text-gray-400">{{ $votante->barrio }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                @if($votante->mesa)
                                    <div class="text-sm font-medium">Mesa {{ $votante->mesa }}</div>
                                    @if($votante->orden)
                                        <div class="text-xs text-gray-400">Orden {{ $votante->orden }}</div>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <div>{{ $votante->distrito ?? '-' }}</div>
                                @if($votante->departamento && $votante->departamento !== $votante->distrito)
                                    <div class="text-xs text-gray-400">{{ $votante->departamento }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $votante->lider->usuario->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($votante->codigo_intencion === 'A') bg-green-100 text-green-800
                                    @elseif($votante->codigo_intencion === 'B') bg-blue-100 text-blue-800
                                    @elseif($votante->codigo_intencion === 'C') bg-yellow-100 text-yellow-800
                                    @elseif($votante->codigo_intencion === 'D') bg-orange-100 text-orange-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $votante->codigo_intencion }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $votante->estado_contacto }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    @if(!$votante->ya_voto)
                                        <button wire:click="marcarVoto({{ $votante->id }})" 
                                                wire:confirm="¿Confirmar que este votante ya votó?"
                                                class="text-green-600 hover:text-green-900" title="Marcar voto">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    @endif
                                    
                                    <a href="{{ route('votantes.edit', $votante->id) }}" class="text-blue-600 hover:text-blue-900" title="Editar">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                        </svg>
                                    </a>
                                    
                                    <button wire:click="eliminarVotante({{ $votante->id }})" 
                                            wire:confirm="¿Está seguro de eliminar este votante?"
                                            class="text-red-600 hover:text-red-900" title="Eliminar">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                No se encontraron votantes
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
            {{ $votantes->links() }}
        </div>
    </div>
</div>
