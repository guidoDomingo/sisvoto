<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Gestión de Viajes</h1>
                <p class="mt-1 text-sm text-gray-600">Administre todos los viajes de transporte de votantes</p>
            </div>
            <a href="{{ route('viajes.planner') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Planificar Nuevo Viaje
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded relative">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded relative">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Búsqueda -->
            <div class="lg:col-span-2">
                <input wire:model.live.debounce.300ms="busqueda" 
                       type="text" 
                       placeholder="Buscar por punto, destino, chofer..."
                       class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
            </div>

            <!-- Estado -->
            <div>
                <select wire:model.live="filtroEstado" 
                        class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    <option value="">Todos los estados</option>
                    <option value="Planificado">Planificado</option>
                    <option value="Confirmado">Confirmado</option>
                    <option value="En curso">En curso</option>
                    <option value="Completado">Completado</option>
                    <option value="Cancelado">Cancelado</option>
                </select>
            </div>

            <!-- Líder -->
            @if (!Auth::user()->hasRole('Líder'))
            <div>
                <select wire:model.live="filtroLider" 
                        class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    <option value="">Todos los líderes</option>
                    @foreach($lideres as $lider)
                        <option value="{{ $lider->id }}">{{ $lider->usuario->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Fecha -->
            <div>
                <input wire:model.live="filtroFecha" 
                       type="date" 
                       class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
            </div>

            <!-- Limpiar -->
            <div class="flex items-end">
                <button wire:click="limpiarFiltros" 
                        class="w-full px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Limpiar
                </button>
            </div>
        </div>
    </div>

    <!-- Tabla de Viajes -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha/Hora</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Chofer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vehículo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pasajeros</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Líder</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Costo</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($viajes as $viaje)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($viaje->fecha_viaje)->format('d/m/Y') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $viaje->hora_salida ? \Carbon\Carbon::parse($viaje->hora_salida)->format('H:i') : '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $viaje->chofer->nombre_completo }}</div>
                                <div class="text-sm text-gray-500">{{ $viaje->chofer->telefono }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $viaje->vehiculo->marca }} {{ $viaje->vehiculo->modelo }}</div>
                                <div class="text-sm text-gray-500">{{ $viaje->vehiculo->placa }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $viaje->votantes->count() }} / {{ $viaje->vehiculo->capacidad_pasajeros }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $viaje->liderResponsable ? $viaje->liderResponsable->usuario->name : 'Sin asignar' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    @if($viaje->estado === 'Planificado') bg-gray-100 text-gray-800
                                    @elseif($viaje->estado === 'Confirmado') bg-blue-100 text-blue-800
                                    @elseif($viaje->estado === 'En curso') bg-yellow-100 text-yellow-800
                                    @elseif($viaje->estado === 'Completado') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $viaje->estado }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                ₲ {{ number_format($viaje->costo_total, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="verDetalles({{ $viaje->id }})" 
                                        class="text-primary-600 hover:text-primary-900 mr-3">
                                    Ver
                                </button>
                                @if($viaje->estado === 'Planificado')
                                    <button wire:click="cambiarEstado({{ $viaje->id }}, 'Confirmado')" 
                                            class="text-green-600 hover:text-green-900 mr-3">
                                        Confirmar
                                    </button>
                                @endif
                                @if(in_array($viaje->estado, ['Confirmado', 'En curso']))
                                    <button wire:click="marcarCompletado({{ $viaje->id }})" 
                                            class="text-blue-600 hover:text-blue-900">
                                        Completar
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">No hay viajes registrados</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $viajes->links() }}
        </div>
    </div>

    <!-- Modal de Detalles -->
    @if($mostrarModal && $viajeSeleccionado)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div wire:click="cerrarModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full z-50">
                    <!-- Header -->
                    <div class="bg-white px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Detalles del Viaje</h3>
                            <button wire:click="cerrarModal" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="bg-white px-6 py-4">
                        <div class="grid grid-cols-2 gap-6">
                            <!-- Información General -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Información General</h4>
                                <dl class="space-y-2">
                                    <div>
                                        <dt class="text-xs text-gray-500">Fecha y Hora</dt>
                                        <dd class="text-sm font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($viajeSeleccionado->fecha_viaje)->format('d/m/Y') }} - 
                                            {{ $viajeSeleccionado->hora_salida ? \Carbon\Carbon::parse($viajeSeleccionado->hora_salida)->format('H:i') : '-' }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-gray-500">Estado</dt>
                                        <dd class="text-sm">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                @if($viajeSeleccionado->estado === 'Planificado') bg-gray-100 text-gray-800
                                                @elseif($viajeSeleccionado->estado === 'Confirmado') bg-blue-100 text-blue-800
                                                @elseif($viajeSeleccionado->estado === 'En curso') bg-yellow-100 text-yellow-800
                                                @elseif($viajeSeleccionado->estado === 'Completado') bg-green-100 text-green-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ $viajeSeleccionado->estado }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-gray-500">Punto de Partida</dt>
                                        <dd class="text-sm font-medium text-gray-900">{{ $viajeSeleccionado->punto_partida }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-gray-500">Destino</dt>
                                        <dd class="text-sm font-medium text-gray-900">{{ $viajeSeleccionado->destino }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-gray-500">Distancia Estimada</dt>
                                        <dd class="text-sm font-medium text-gray-900">{{ number_format($viajeSeleccionado->distancia_estimada_km, 2) }} km</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-gray-500">Líder Responsable</dt>
                                        <dd class="text-sm font-medium text-gray-900">{{ $viajeSeleccionado->liderResponsable ? $viajeSeleccionado->liderResponsable->usuario->name : 'Sin asignar' }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Vehículo y Chofer -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Vehículo y Chofer</h4>
                                <dl class="space-y-2">
                                    <div>
                                        <dt class="text-xs text-gray-500">Vehículo</dt>
                                        <dd class="text-sm font-medium text-gray-900">
                                            {{ $viajeSeleccionado->vehiculo->marca }} {{ $viajeSeleccionado->vehiculo->modelo }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-gray-500">Placa</dt>
                                        <dd class="text-sm font-medium text-gray-900">{{ $viajeSeleccionado->vehiculo->placa }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-gray-500">Capacidad</dt>
                                        <dd class="text-sm font-medium text-gray-900">
                                            {{ $viajeSeleccionado->votantes->count() }} / {{ $viajeSeleccionado->vehiculo->capacidad_pasajeros }} pasajeros
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-gray-500">Chofer</dt>
                                        <dd class="text-sm font-medium text-gray-900">{{ $viajeSeleccionado->chofer->nombre_completo }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-gray-500">Teléfono Chofer</dt>
                                        <dd class="text-sm font-medium text-gray-900">{{ $viajeSeleccionado->chofer->telefono }}</dd>
                                    </div>
                                </dl>

                                <h4 class="text-sm font-medium text-gray-900 mt-4 mb-3">Costos</h4>
                                <dl class="space-y-2">
                                    <div>
                                        <dt class="text-xs text-gray-500">Combustible</dt>
                                        <dd class="text-sm font-medium text-gray-900">₲ {{ number_format($viajeSeleccionado->costo_combustible, 0, ',', '.') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-gray-500">Chofer</dt>
                                        <dd class="text-sm font-medium text-gray-900">₲ {{ number_format($viajeSeleccionado->costo_chofer, 0, ',', '.') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-gray-500">Viáticos</dt>
                                        <dd class="text-sm font-medium text-gray-900">₲ {{ number_format($viajeSeleccionado->viaticos, 0, ',', '.') }}</dd>
                                    </div>
                                    <div class="pt-2 border-t border-gray-200">
                                        <dt class="text-xs text-gray-500">Total</dt>
                                        <dd class="text-base font-bold text-gray-900">₲ {{ number_format($viajeSeleccionado->costo_total, 0, ',', '.') }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Lista de Pasajeros -->
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Pasajeros ({{ $viajeSeleccionado->votantes->count() }})</h4>
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Nombre</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">CI</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Teléfono</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Barrio</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($viajeSeleccionado->votantes as $votante)
                                            <tr>
                                                <td class="px-4 py-2 text-sm text-gray-900">{{ $votante->nombres }} {{ $votante->apellidos }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-600">{{ $votante->ci }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-600">{{ $votante->telefono }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-600">{{ $votante->barrio }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        @if($viajeSeleccionado->notas)
                            <div class="mt-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Observaciones</h4>
                                <p class="text-sm text-gray-600">{{ $viajeSeleccionado->notas }}</p>
                            </div>
                        @endif>
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50 px-6 py-4 flex justify-between">
                        <div class="space-x-2">
                            @if($viajeSeleccionado->estado === 'Planificado')
                                <button wire:click="cambiarEstado({{ $viajeSeleccionado->id }}, 'Confirmado')" 
                                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                    Confirmar Viaje
                                </button>
                                <button wire:click="eliminarViaje({{ $viajeSeleccionado->id }})" 
                                        wire:confirm="¿Está seguro de eliminar este viaje?"
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                    Eliminar
                                </button>
                            @elseif(in_array($viajeSeleccionado->estado, ['Confirmado', 'En curso']))
                                <button wire:click="marcarCompletado({{ $viajeSeleccionado->id }})" 
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    Marcar como Completado
                                </button>
                                <button wire:click="cancelarViaje({{ $viajeSeleccionado->id }})" 
                                        wire:confirm="¿Está seguro de cancelar este viaje?"
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                    Cancelar Viaje
                                </button>
                            @endif
                        </div>
                        <button wire:click="cerrarModal" 
                                class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
