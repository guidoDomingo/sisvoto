<div>
    <!-- Dashboard del Líder -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Mi Dashboard - {{ $lider->territorio }}</h1>
                <p class="mt-1 text-sm text-gray-600">Resumen de tu territorio y acciones rápidas</p>
            </div>
            <button wire:click="exportarLista" 
                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
                Exportar Lista
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div role="alert" class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded relative">
            {{ session('message') }}
        </div>
    @endif

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500 mb-1">Total Asignados</div>
            <div class="text-3xl font-bold text-gray-900">{{ $estadisticas['total_asignados'] }}</div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500 mb-1">Contactados</div>
            <div class="text-3xl font-bold text-blue-600">{{ $estadisticas['contactados'] }}</div>
            <div class="text-xs text-gray-500 mt-1">
                {{ $estadisticas['total_asignados'] > 0 ? number_format(($estadisticas['contactados'] / $estadisticas['total_asignados']) * 100, 1) : 0 }}%
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500 mb-1">Intención A/B</div>
            <div class="text-3xl font-bold text-green-600">{{ $estadisticas['intencion_a_b'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Seguros + Probables</div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500 mb-1">Necesitan Transporte</div>
            <div class="text-3xl font-bold text-yellow-600">{{ $estadisticas['necesitan_transporte'] }}</div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500 mb-1">Ya Votaron</div>
            <div class="text-3xl font-bold text-purple-600">{{ $estadisticas['ya_votaron'] }}</div>
            <div class="text-xs text-gray-500 mt-1">
                {{ $estadisticas['total_asignados'] > 0 ? number_format(($estadisticas['ya_votaron'] / $estadisticas['total_asignados']) * 100, 1) : 0 }}%
            </div>
        </div>
    </div>

    <!-- Predicción -->
    @if(count($prediccion) > 0)
    <div class="bg-gradient-to-r from-blue-500 to-blue-700 rounded-lg shadow-lg p-6 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold mb-2">Predicción de Votos</h3>
                <div class="text-4xl font-bold">{{ number_format($prediccion['votos_estimados'] ?? 0, 1) }}</div>
                <p class="text-blue-100 text-sm mt-1">votos estimados de tu territorio</p>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold">{{ number_format($prediccion['porcentaje_estimado'] ?? 0, 1) }}%</div>
                <p class="text-blue-100 text-sm">tasa de conversión</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Votantes Recientes -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Votantes Recientes</h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @forelse($votantesRecientes as $votante)
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $votante->nombres }} {{ $votante->apellidos }}</p>
                                <p class="text-xs text-gray-500">{{ $votante->barrio }} • CI: {{ $votante->ci }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-1 text-xs rounded-full font-semibold
                                    @if($votante->codigo_intencion === 'A') bg-green-100 text-green-800
                                    @elseif($votante->codigo_intencion === 'B') bg-blue-100 text-blue-800
                                    @elseif($votante->codigo_intencion === 'C') bg-yellow-100 text-yellow-800
                                    @elseif($votante->codigo_intencion === 'D') bg-orange-100 text-orange-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $votante->codigo_intencion }}
                                </span>
                                @if(!$votante->ya_voto)
                                    <button wire:click="abrirModalContacto({{ $votante->id }})" 
                                            class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">No hay votantes recientes</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Votantes Críticos -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 bg-red-50">
                <h3 class="text-lg font-semibold text-red-900">⚠️ Requieren Atención</h3>
            </div>
            <div class="p-6">
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($votantesCriticos as $votante)
                        <div class="flex items-center justify-between p-3 border border-red-200 rounded-lg bg-red-50">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $votante->nombres }} {{ $votante->apellidos }}</p>
                                <p class="text-xs text-gray-600">
                                    {{ $votante->telefono }} • {{ $votante->barrio }}
                                </p>
                                <p class="text-xs text-red-600 mt-1">
                                    @if($votante->estado_contacto === 'Crítico')
                                        Estado crítico - Requiere seguimiento urgente
                                    @else
                                        Sin contactar - Registrado hace {{ \Carbon\Carbon::parse($votante->created_at)->diffForHumans() }}
                                    @endif
                                </p>
                            </div>
                            <div class="flex flex-col gap-2">
                                <button wire:click="abrirModalContacto({{ $votante->id }})" 
                                        class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded">
                                    Contactar
                                </button>
                                @if(!$votante->ya_voto)
                                    <button wire:click="marcarVoto({{ $votante->id }})" 
                                            wire:confirm="¿Confirmar voto?"
                                            class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs rounded">
                                        Votó
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">¡Excelente! No hay votantes críticos</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Registro de Contacto -->
    @if($showContactoModal && $votanteSeleccionado)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Registrar Contacto</h3>
                    <p class="text-sm text-gray-600">{{ $votanteSeleccionado->nombres }} {{ $votanteSeleccionado->apellidos }}</p>
                </div>
                
                <form wire:submit="registrarContacto">
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Método de Contacto *</label>
                            <select wire:model="metodoContacto" required 
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="Llamada">Llamada telefónica</option>
                                <option value="Puerta a puerta">Puerta a puerta</option>
                                <option value="WhatsApp">WhatsApp</option>
                                <option value="Evento">En evento</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Resultado del Contacto *</label>
                            <textarea wire:model="resultadoContacto" required rows="3" 
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Describe cómo fue el contacto..."></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nueva Intención de Voto *</label>
                            <select wire:model="nuevaIntencion" required 
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="A">A - Voto seguro</option>
                                <option value="B">B - Probable</option>
                                <option value="C">C - Indeciso</option>
                                <option value="D">D - Difícil</option>
                                <option value="E">E - Contrario</option>
                            </select>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 rounded-b-lg">
                        <button type="button" 
                                wire:click="$set('showContactoModal', false)"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                            Guardar Contacto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
