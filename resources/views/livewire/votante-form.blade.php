<div>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ $votanteId ? 'Editar Votante' : 'Nuevo Votante' }}</h1>
        <p class="mt-1 text-sm text-gray-600">Complete el formulario para {{ $votanteId ? 'actualizar' : 'registrar' }} el votante</p>
    </div>

    <!-- Form -->
    <form wire:submit="guardar">
        <div class="bg-white rounded-lg shadow p-6">
            <!-- Datos Personales -->
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Datos Personales</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">CI *</label>
                        <div class="space-y-2">
                            <div class="flex gap-2">
                                <div class="flex-1 relative">
                                    <input wire:model.live.debounce.500ms="ci" type="text" required 
                                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 pr-10"
                                        placeholder="Ej: 1234567"
                                        maxlength="20">
                                    @if($buscandoDatos)
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <svg class="animate-spin h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <button type="button" wire:click="buscarVotanteManual" 
                                        wire:loading.attr="disabled"
                                        wire:target="buscarVotanteManual"
                                        class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition flex items-center gap-2">
                                    <span wire:loading.remove wire:target="buscarVotanteManual">🏠 Local</span>
                                    <span wire:loading wire:target="buscarVotanteManual" class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Buscando...
                                    </span>
                                </button>
                                <button type="button" wire:click="consultarTSJE" 
                                        wire:loading.attr="disabled"
                                        wire:target="consultarTSJE"
                                        class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition flex items-center gap-2">
                                    <span wire:loading.remove wire:target="consultarTSJE">🔍 TSJE</span>
                                    <span wire:loading wire:target="consultarTSJE" class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Buscando...
                                    </span>
                                </button>
                            </div>
                            
                            <!-- Opción de búsqueda automática -->
                            <div class="flex items-center justify-between">
                                <label class="flex items-center text-sm text-gray-600">
                                    <input type="checkbox" wire:model.live="busquedaAutomatica" 
                                           class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    Búsqueda automática al escribir CI
                                </label>
                                @if(session('tsje_success'))
                                    <span class="text-sm text-green-600 font-medium">{{ session('tsje_success') }}</span>
                                @endif
                            </div>
                        </div>
                        @error('ci') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        
                        @if($mensajeBusqueda)
                            <div class="mt-2 p-3 rounded-lg text-sm {{ $datosEncontrados ? 'bg-green-50 border border-green-200 text-green-800' : (str_contains($mensajeBusqueda, 'Error') || str_contains($mensajeBusqueda, '❌') ? 'bg-red-50 border border-red-200 text-red-800' : 'bg-blue-50 border border-blue-200 text-blue-800') }}">
                                <div class="flex items-start gap-2">
                                    @if($buscandoDatos)
                                        <svg class="animate-spin h-4 w-4 mt-0.5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    @endif
                                    <span>{{ $mensajeBusqueda }}</span>
                                </div>
                                @if($datosEncontrados)
                                    <div class="mt-2 text-xs text-green-600">
                                        💡 Los campos se llenaron automáticamente. Puede modificarlos si es necesario.
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input wire:model="telefono" type="text" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('telefono') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div></div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombres *</label>
                        <input wire:model="nombres" type="text" required 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('nombres') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos *</label>
                        <input wire:model="apellidos" type="text" required 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('apellidos') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input wire:model="email" type="email" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Datos Electorales TSJE -->
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Datos Electorales (TSJE)</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nº Registro</label>
                        <input wire:model="nro_registro" type="text" readonly
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-gray-50">
                        @error('nro_registro') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mesa</label>
                        <input wire:model="mesa" type="number" readonly
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-gray-50">
                        @error('mesa') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Orden en Lista</label>
                        <input wire:model="orden" type="number" readonly
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-gray-50">
                        @error('orden') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Local de Votación</label>
                        <input wire:model="descripcion_local" type="text" readonly
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-gray-50">
                        <div class="text-xs text-gray-500 mt-1">Código: {{ $local_votacion ?? 'N/A' }}</div>
                        @error('descripcion_local') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento</label>
                        <input wire:model="fecha_nacimiento" type="date"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('fecha_nacimiento') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Afiliación</label>
                        <input wire:model="fecha_afiliacion" type="date"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('fecha_afiliacion') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Ubicación Política TSJE -->
                    <div class="md:col-span-3">
                        <h4 class="text-base font-medium text-gray-800 mb-3 mt-4">Ubicación Política Electoral</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Departamento</label>
                                <div class="space-y-1">
                                    <input wire:model="departamento" type="text" readonly
                                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-gray-50">
                                    <div class="text-xs text-gray-500">Código: {{ $codigo_departamento ?? 'N/A' }}</div>
                                </div>
                                @error('departamento') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Distrito TSJE</label>
                                <div class="space-y-1">
                                    <input wire:model="distrito" type="text" readonly
                                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-gray-50">
                                    <div class="text-xs text-gray-500">Código: {{ $codigo_distrito ?? 'N/A' }}</div>
                                </div>
                                @error('distrito') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Barrio TSJE</label>
                                <div class="space-y-1">
                                    <input wire:model="barrio_tsje" type="text" readonly
                                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-gray-50">
                                    <div class="text-xs text-gray-500">Código: {{ $codigo_barrio ?? 'N/A' }}</div>
                                </div>
                                @error('barrio_tsje') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dirección -->
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Dirección</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                        <input wire:model="direccion" type="text" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('direccion') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Barrio</label>
                        <input wire:model="barrio" type="text" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('barrio') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Zona</label>
                        <input wire:model="zona" type="text" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('zona') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Distrito</label>
                        <input wire:model="distrito" type="text" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('distrito') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Latitud</label>
                        <input wire:model="latitud" type="number" step="0.000001" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('latitud') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Longitud</label>
                        <input wire:model="longitud" type="number" step="0.000001" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('longitud') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Datos de Campaña -->
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Datos de Campaña</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Líder Asignado *</label>
                        <select wire:model="lider_asignado_id" required 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Seleccione un líder</option>
                            @foreach($lideres as $lider)
                                <option value="{{ $lider->id }}">{{ $lider->usuario->name }} - {{ $lider->territorio }}</option>
                            @endforeach
                        </select>
                        @error('lider_asignado_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Código de Intención *</label>
                        <select wire:model="codigo_intencion" required 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="A">A - Voto seguro</option>
                            <option value="B">B - Probable</option>
                            <option value="C">C - Indeciso</option>
                            <option value="D">D - Difícil</option>
                            <option value="E">E - Contrario</option>
                        </select>
                        @error('codigo_intencion') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado de Contacto *</label>
                        <select wire:model="estado_contacto" required 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="Nuevo">Nuevo</option>
                            <option value="Contactado">Contactado</option>
                            <option value="Re-contacto">Re-contacto</option>
                            <option value="Comprometido">Comprometido</option>
                            <option value="Crítico">Crítico</option>
                        </select>
                        @error('estado_contacto') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="flex items-center mt-6">
                            <input wire:model="necesita_transporte" type="checkbox" 
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm font-medium text-gray-700">Necesita transporte</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Notas -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                <textarea wire:model="notas" rows="3" 
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                @error('notas') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('votantes.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    {{ $votanteId ? 'Actualizar' : 'Guardar' }} Votante
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-format CI input
    const ciInput = document.querySelector('input[wire\\:model\\.live\\.debounce\\.500ms="ci"]');
    if (ciInput) {
        ciInput.addEventListener('input', function(e) {
            // Remove non-numeric characters
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Limit to reasonable CI length
            if (this.value.length > 15) {
                this.value = this.value.substring(0, 15);
            }
        });
    }

    // Auto-format phone number
    const phoneInput = document.querySelector('input[wire\\:model="telefono"]');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            // Remove non-numeric characters except + and spaces
            this.value = this.value.replace(/[^0-9+\s]/g, '');
        });
    }

    // Auto-capitalize names
    const nameInputs = document.querySelectorAll('input[wire\\:model="nombres"], input[wire\\:model="apellidos"]');
    nameInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            // Capitalize first letter of each word
            this.value = this.value.toLowerCase().replace(/\b\w/g, l => l.toUpperCase());
        });
    });
});
</script>

@push('styles')
<style>
.animate-pulse-slow {
    animation: pulse 2s infinite;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.fade-in {
    animation: fadeIn 0.3s ease-out;
}
</style>
@endpush
