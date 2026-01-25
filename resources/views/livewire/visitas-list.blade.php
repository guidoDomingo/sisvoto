<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Gestión de Visitas</h2>
                    <button wire:click="abrirModal" 
                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Nueva Visita
                    </button>
                </div>

                @if (session()->has('message'))
                    <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ session('message') }}
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Filtros -->
                <div class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <input wire:model.live="busqueda" 
                               type="text" 
                               placeholder="Buscar votante..."
                               class="w-full px-3 py-2 border rounded">
                    </div>
                    <div>
                        <select wire:model.live="filtroTipo" class="w-full px-3 py-2 border rounded">
                            <option value="">Todos los tipos</option>
                            <option value="Primera visita">Primera visita</option>
                            <option value="Seguimiento">Seguimiento</option>
                            <option value="Convencimiento">Convencimiento</option>
                            <option value="Confirmación">Confirmación</option>
                            <option value="Urgente">Urgente</option>
                        </select>
                    </div>
                    <div>
                        <select wire:model.live="filtroResultado" class="w-full px-3 py-2 border rounded">
                            <option value="">Todos los resultados</option>
                            <option value="Favorable">Favorable</option>
                            <option value="Indeciso">Indeciso</option>
                            <option value="No favorable">No favorable</option>
                            <option value="No contactado">No contactado</option>
                            <option value="Rechazado">Rechazado</option>
                        </select>
                    </div>
                    <div>
                        <input wire:model.live="filtroFecha" 
                               type="date" 
                               class="w-full px-3 py-2 border rounded">
                    </div>
                    <div>
                        <button wire:click="limpiarFiltros" 
                                class="w-full px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                            Limpiar
                        </button>
                    </div>
                </div>

                <!-- Tabla -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Votante</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Líder</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Resultado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Seguimiento</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($visitas as $visita)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ $visita->fecha_visita->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $visita->votante->nombres }} {{ $visita->votante->apellidos }}
                                        </div>
                                        <div class="text-sm text-gray-500">CI: {{ $visita->votante->ci }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ $visita->lider ? $visita->lider->usuario->name : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded
                                            @if($visita->tipo_visita === 'Primera visita') bg-blue-100 text-blue-800
                                            @elseif($visita->tipo_visita === 'Seguimiento') bg-green-100 text-green-800
                                            @elseif($visita->tipo_visita === 'Convencimiento') bg-yellow-100 text-yellow-800
                                            @elseif($visita->tipo_visita === 'Confirmación') bg-purple-100 text-purple-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ $visita->tipo_visita }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($visita->resultado)
                                            <span class="px-2 py-1 text-xs rounded
                                                @if($visita->resultado === 'Favorable') bg-green-100 text-green-800
                                                @elseif($visita->resultado === 'Indeciso') bg-yellow-100 text-yellow-800
                                                @elseif($visita->resultado === 'No favorable') bg-orange-100 text-orange-800
                                                @elseif($visita->resultado === 'No contactado') bg-gray-100 text-gray-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ $visita->resultado }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-xs">Sin resultado</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($visita->requiere_seguimiento)
                                            <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded">
                                                ⚠️ Urgente
                                            </span>
                                            @if($visita->proxima_visita)
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ $visita->proxima_visita->format('d/m/Y') }}
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <button wire:click="editarVisita({{ $visita->id }})" 
                                                class="text-blue-600 hover:text-blue-900 mr-3">
                                            Editar
                                        </button>
                                        <button wire:click="eliminarVisita({{ $visita->id }})" 
                                                onclick="return confirm('¿Eliminar esta visita?')"
                                                class="text-red-600 hover:text-red-900">
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        No se encontraron visitas
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="mt-4">
                    {{ $visitas->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nueva/Editar Visita -->
    @if($mostrarModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-10 mx-auto p-8 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-800">{{ $visitaId ? 'Editar' : 'Nueva' }} Visita</h3>
                    <button wire:click="cerrarModal" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="guardarVisita">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Votante -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Votante *</label>
                            <select wire:model.live="votante_id" class="w-full px-3 py-2 border rounded" required>
                                <option value="">Seleccionar votante</option>
                                @foreach($votantes as $votante)
                                    <option value="{{ $votante->id }}">
                                        {{ $votante->apellidos }}, {{ $votante->nombres }} - CI: {{ $votante->ci }}
                                    </option>
                                @endforeach
                            </select>
                            @error('votante_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Líder -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Líder *</label>
                            <select wire:model="lider_id" class="w-full px-3 py-2 border rounded" required 
                                    @if(Auth::user()->hasRole('Líder')) disabled @endif>
                                <option value="">Seleccionar líder</option>
                                @foreach($lideres as $lider)
                                    <option value="{{ $lider->id }}">{{ $lider->usuario->name }}</option>
                                @endforeach
                            </select>
                            @error('lider_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Fecha -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha y Hora *</label>
                            <input wire:model="fecha_visita" type="datetime-local" 
                                   class="w-full px-3 py-2 border rounded" required>
                            @error('fecha_visita') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Tipo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Visita *</label>
                            <select wire:model="tipo_visita" class="w-full px-3 py-2 border rounded" required>
                                <option value="Primera visita">Primera visita</option>
                                <option value="Seguimiento">Seguimiento</option>
                                <option value="Convencimiento">Convencimiento</option>
                                <option value="Confirmación">Confirmación</option>
                                <option value="Urgente">Urgente</option>
                            </select>
                            @error('tipo_visita') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Resultado -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Resultado</label>
                            <select wire:model="resultado" class="w-full px-3 py-2 border rounded">
                                <option value="">Sin definir</option>
                                <option value="Favorable">Favorable</option>
                                <option value="Indeciso">Indeciso</option>
                                <option value="No favorable">No favorable</option>
                                <option value="No contactado">No contactado</option>
                                <option value="Rechazado">Rechazado</option>
                            </select>
                            @error('resultado') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Duración -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Duración (minutos)</label>
                            <input wire:model="duracion_minutos" type="number" step="0.01" min="0"
                                   class="w-full px-3 py-2 border rounded" placeholder="Ej: 30">
                            @error('duracion_minutos') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Próxima Visita -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Próxima Visita</label>
                            <input wire:model="proxima_visita" type="datetime-local" 
                                   class="w-full px-3 py-2 border rounded">
                            @error('proxima_visita') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Requiere Seguimiento -->
                        <div class="flex items-center">
                            <label class="flex items-center cursor-pointer">
                                <input wire:model="requiere_seguimiento" type="checkbox" class="mr-2">
                                <span class="text-sm font-medium text-gray-700">⚠️ Requiere seguimiento urgente</span>
                            </label>
                        </div>

                        <!-- Observaciones -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                            <textarea wire:model="observaciones" rows="3" 
                                      class="w-full px-3 py-2 border rounded" 
                                      placeholder="Detalles de la visita..."></textarea>
                            @error('observaciones') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Compromisos -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Compromisos</label>
                            <textarea wire:model="compromisos" rows="2" 
                                      class="w-full px-3 py-2 border rounded" 
                                      placeholder="Compromisos adquiridos..."></textarea>
                            @error('compromisos') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Foto Evidencia -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Foto Evidencia</label>
                            <input wire:model="foto_evidencia" type="file" accept="image/*" 
                                   class="w-full px-3 py-2 border rounded">
                            @error('foto_evidencia') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            
                            @if ($foto_evidencia)
                                <div class="mt-2">
                                    <img src="{{ $foto_evidencia->temporaryUrl() }}" class="h-32 rounded">
                                </div>
                            @endif
                        </div>

                        <!-- Ubicación del Votante -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">📍 Ubicación del Votante</label>
                            
                            @if($votante_id)
                                @php
                                    $votanteSeleccionado = \App\Models\Votante::find($votante_id);
                                @endphp
                                
                                @if($votanteSeleccionado)
                                    <div class="border rounded-lg p-4 bg-gray-50">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                            <div>
                                                <span class="text-xs text-gray-500">Dirección:</span>
                                                <p class="text-sm font-medium">{{ $votanteSeleccionado->direccion ?: 'No especificada' }}</p>
                                            </div>
                                            <div>
                                                <span class="text-xs text-gray-500">Barrio:</span>
                                                <p class="text-sm font-medium">{{ $votanteSeleccionado->barrio ?: 'No especificado' }}</p>
                                            </div>
                                            <div>
                                                <span class="text-xs text-gray-500">Zona:</span>
                                                <p class="text-sm font-medium">{{ $votanteSeleccionado->zona ?: 'No especificada' }}</p>
                                            </div>
                                            <div>
                                                <span class="text-xs text-gray-500">Distrito:</span>
                                                <p class="text-sm font-medium">{{ $votanteSeleccionado->distrito ?: 'No especificado' }}</p>
                                            </div>
                                        </div>
                                        
                                        <!-- Mapa Interactivo -->
                                        <div class="mt-3">
                                            <!-- Buscador -->
                                            <div class="mb-3">
                                                <div class="flex gap-2">
                                                    <input 
                                                        type="text" 
                                                        id="mapSearch" 
                                                        placeholder="🔍 Buscar ciudad, barrio, dirección o coordenadas (lat, lng)..."
                                                        class="flex-1 px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                        onkeydown="if(event.key === 'Enter') { event.preventDefault(); searchLocation(); }">
                                                    <button 
                                                        type="button"
                                                        onclick="searchLocation()" 
                                                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-sm font-medium">
                                                        Buscar
                                                    </button>
                                                </div>
                                                <div id="searchResults" class="mt-2 text-xs text-gray-600"></div>
                                            </div>
                                            
                                            <div id="visitaMap" 
                                                 wire:ignore
                                                 style="width: 100%; height: 400px; border-radius: 0.5rem; border: 2px solid #10b981;"
                                                 data-lat="{{ $latitud_seleccionada ?? ($votanteSeleccionado->latitud ?? -25.2637) }}"
                                                 data-lng="{{ $longitud_seleccionada ?? ($votanteSeleccionado->longitud ?? -57.5759) }}"
                                                 data-has-coords="{{ ($latitud_seleccionada && $longitud_seleccionada) || ($votanteSeleccionado->latitud && $votanteSeleccionado->longitud) ? '1' : '0' }}">
                                            </div>
                                            <p class="text-xs text-gray-500 mt-2">
                                                💡 Haz clic en el mapa | 🔍 Busca lugares | 🎯 Arrastra el marcador | 🗺️ Los límites se cargan automáticamente
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <div class="border rounded-lg p-4 bg-red-50 text-center">
                                        <p class="text-sm text-red-600">❌ No se encontró el votante</p>
                                    </div>
                                @endif
                            @else
                                <div class="border rounded-lg p-4 bg-gray-50 text-center">
                                    <p class="text-sm text-gray-500">Selecciona un votante para ver/editar su ubicación</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-8 pt-4 border-t">
                        <button type="button" wire:click="cerrarModal" 
                                class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-5 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition font-medium">
                            {{ $visitaId ? 'Actualizar' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    let visitaMap = null;
    let visitaMarker = null;
    let selectedLat = null;
    let selectedLng = null;
    let boundaryLayers = L.layerGroup();
    
    // Función para buscar ubicación
    window.searchLocation = async function() {
        const searchInput = document.getElementById('mapSearch');
        const resultsDiv = document.getElementById('searchResults');
        const query = searchInput.value.trim();
        
        if (!query) {
            resultsDiv.innerHTML = '⚠️ Ingresa una búsqueda';
            return;
        }
        
        resultsDiv.innerHTML = '🔍 Buscando...';
        
        // Verificar si es coordenadas (lat, lng)
        const coordMatch = query.match(/^(-?\d+\.?\d*),\s*(-?\d+\.?\d*)$/);
        if (coordMatch) {
            const lat = parseFloat(coordMatch[1]);
            const lng = parseFloat(coordMatch[2]);
            
            if (visitaMap) {
                visitaMap.setView([lat, lng], 15);
                
                if (visitaMarker) {
                    visitaMarker.setLatLng([lat, lng]);
                } else {
                    const icon = L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    });
                    visitaMarker = L.marker([lat, lng], { draggable: true, icon: icon }).addTo(visitaMap);
                }
                
                selectedLat = lat;
                selectedLng = lng;
                visitaMarker.bindPopup(`📍 ${lat.toFixed(6)}, ${lng.toFixed(6)}`).openPopup();
                resultsDiv.innerHTML = `✅ Coordenadas: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                
                // Cargar límites del área
                loadBoundaries(lat, lng);
            }
            return;
        }
        
        // Buscar con Nominatim (OpenStreetMap) - Búsqueda mejorada para Bolivia
        try {
            // Agregar "Paraguay" a la búsqueda para mejor precisión
            const searchQuery = query.toLowerCase().includes('paraguay') ? query : `${query}, Paraguay`;
            
            const response = await fetch(`https://nominatim.openstreetmap.org/search?` + 
                `format=json` +
                `&q=${encodeURIComponent(searchQuery)}` +
                `&countrycodes=py` +
                `&addressdetails=1` +
                `&limit=8` +
                `&accept-language=es`
            );
            const data = await response.json();
            
            if (data.length === 0) {
                resultsDiv.innerHTML = '❌ No se encontraron resultados. Intenta: "Asunción", "Ciudad del Este", "Encarnación"';
                return;
            }
            
            // Mostrar múltiples resultados si hay más de uno
            if (data.length > 1) {
                let html = '<div class="bg-white border rounded-lg shadow-sm p-2 mt-1 max-h-48 overflow-y-auto">';
                html += '<div class="font-semibold text-xs mb-1 text-gray-700">Selecciona una ubicación:</div>';
                data.forEach((result, index) => {
                    const name = result.display_name.split(',').slice(0, 3).join(',');
                    html += `<div onclick="selectSearchResult(${result.lat}, ${result.lon}, '${result.display_name.replace(/'/g, "\\'")}', ${result.osm_id}, '${result.osm_type}')" 
                        class="cursor-pointer hover:bg-blue-50 p-2 rounded text-xs border-b last:border-0">
                        <span class="text-blue-600 font-medium">${index + 1}.</span> ${name}
                    </div>`;
                });
                html += '</div>';
                resultsDiv.innerHTML = html;
            } else {
                // Un solo resultado, ir directo
                const result = data[0];
                selectSearchResult(result.lat, result.lon, result.display_name, result.osm_id, result.osm_type);
            }
        } catch (error) {
            console.error('Error en búsqueda:', error);
            resultsDiv.innerHTML = '❌ Error en la búsqueda. Verifica tu conexión.';
        }
    };
    
    // Función para seleccionar un resultado de búsqueda
    window.selectSearchResult = function(lat, lng, displayName, osmId, osmType) {
        const resultsDiv = document.getElementById('searchResults');
        const latNum = parseFloat(lat);
        const lngNum = parseFloat(lng);
        
        // Actualizar Livewire
        @this.set('latitud_seleccionada', latNum);
        @this.set('longitud_seleccionada', lngNum);
        
        if (visitaMap) {
            visitaMap.setView([latNum, lngNum], 15);
            
            if (visitaMarker) {
                visitaMarker.setLatLng([latNum, lngNum]);
            } else {
                const icon = L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                });
                visitaMarker = L.marker([latNum, lngNum], { draggable: true, icon: icon }).addTo(visitaMap);
                
                // Agregar evento de arrastre
                visitaMarker.on('dragend', function(e) {
                    const position = e.target.getLatLng();
                    selectedLat = position.lat;
                    selectedLng = position.lng;
                    @this.set('latitud_seleccionada', selectedLat);
                    @this.set('longitud_seleccionada', selectedLng);
                    console.log('📍 Marker movido:', selectedLat, selectedLng);
                    visitaMarker.bindPopup(`📍 ${selectedLat.toFixed(6)}, ${selectedLng.toFixed(6)}`).openPopup();
                    loadBoundaries(selectedLat, selectedLng);
                });
            }
            
            selectedLat = latNum;
            selectedLng = lngNum;
            
            const shortName = displayName.split(',').slice(0, 2).join(',');
            visitaMarker.bindPopup(`📍 ${shortName}`).openPopup();
            resultsDiv.innerHTML = `✅ ${displayName}`;
            
            // Cargar límites del área encontrada
            loadBoundaries(latNum, lngNum, osmId, osmType);
        }
    };
    
    // Función para cargar límites de ciudades/barrios
    async function loadBoundaries(lat, lng, osmId = null, osmType = null) {
        // Limpiar capas anteriores
        boundaryLayers.clearLayers();
        
        console.log('🗺️ Cargando límites para:', { lat, lng, osmId, osmType });
        
        try {
            // 1. Si tenemos OSM ID específico, obtener ese polígono
            if (osmId && osmType) {
                const osmTypeMap = { 'node': 'node', 'way': 'way', 'relation': 'rel' };
                const type = osmTypeMap[osmType] || 'rel';
                
                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/lookup?osm_ids=${osmType[0].toUpperCase()}${osmId}&format=json&polygon_geojson=1`);
                    const data = await response.json();
                    
                    if (data.length > 0 && data[0].geojson) {
                        const layer = L.geoJSON(data[0].geojson, {
                            style: {
                                color: '#3b82f6',
                                weight: 3,
                                fillColor: '#93c5fd',
                                fillOpacity: 0.2
                            }
                        }).bindPopup(`📍 ${data[0].display_name}`);
                        
                        boundaryLayers.addLayer(layer);
                        visitaMap.fitBounds(layer.getBounds(), { padding: [50, 50] });
                        console.log('✅ Polígono específico cargado');
                    }
                } catch (e) {
                    console.log('⚠️ No se pudo cargar polígono específico');
                }
            }
            
            // 2. Usar Overpass API para obtener límites administrativos del área
            const overpassQuery = `
                [out:json][timeout:10];
                (
                  // Barrios (admin_level=10,11)
                  relation["boundary"="administrative"]["admin_level"~"^(10|11)$"](around:500,${lat},${lng});
                  // Distritos/Comunas (admin_level=9)
                  relation["boundary"="administrative"]["admin_level"="9"](around:1000,${lat},${lng});
                  // Ciudades (admin_level=8)
                  relation["boundary"="administrative"]["admin_level"="8"](around:2000,${lat},${lng});
                );
                out geom;
            `;
            
            const overpassUrl = 'https://overpass-api.de/api/interpreter';
            const overpassResponse = await fetch(overpassUrl, {
                method: 'POST',
                body: overpassQuery
            });
            
            const overpassData = await overpassResponse.json();
            console.log('📦 Overpass resultados:', overpassData.elements?.length || 0);
            
            if (overpassData.elements && overpassData.elements.length > 0) {
                // Ordenar por admin_level (mayor = más específico)
                const sortedElements = overpassData.elements.sort((a, b) => {
                    return (b.tags.admin_level || 0) - (a.tags.admin_level || 0);
                });
                
                sortedElements.forEach((element, index) => {
                    if (element.type === 'relation' && element.members) {
                        try {
                            // Convertir a GeoJSON
                            const coordinates = [];
                            const ways = element.members.filter(m => m.type === 'way' && m.role === 'outer');
                            
                            ways.forEach(way => {
                                if (way.geometry) {
                                    const coords = way.geometry.map(g => [g.lon, g.lat]);
                                    coordinates.push(coords);
                                }
                            });
                            
                            if (coordinates.length > 0) {
                                const adminLevel = element.tags.admin_level;
                                const name = element.tags.name || 'Sin nombre';
                                
                                // Estilos según nivel administrativo
                                let style = {};
                                if (adminLevel === '10' || adminLevel === '11') {
                                    // Barrios - Verde punteado
                                    style = {
                                        color: '#10b981',
                                        weight: 2,
                                        dashArray: '5, 5',
                                        fillColor: '#6ee7b7',
                                        fillOpacity: 0.15
                                    };
                                } else if (adminLevel === '9') {
                                    // Distritos - Morado
                                    style = {
                                        color: '#8b5cf6',
                                        weight: 2,
                                        dashArray: '8, 4',
                                        fillColor: '#c4b5fd',
                                        fillOpacity: 0.1
                                    };
                                } else if (adminLevel === '8') {
                                    // Ciudades - Naranja
                                    style = {
                                        color: '#f59e0b',
                                        weight: 3,
                                        fillColor: '#fbbf24',
                                        fillOpacity: 0.08
                                    };
                                }
                                
                                const geojson = {
                                    type: 'Feature',
                                    properties: { name: name, admin_level: adminLevel },
                                    geometry: {
                                        type: coordinates.length === 1 ? 'Polygon' : 'MultiPolygon',
                                        coordinates: coordinates.length === 1 ? [coordinates[0]] : coordinates.map(c => [c])
                                    }
                                };
                                
                                const layer = L.geoJSON(geojson, { style: style });
                                layer.bindPopup(`<strong>${adminLevel === '10' || adminLevel === '11' ? 'Barrio' : adminLevel === '9' ? 'Distrito' : 'Ciudad'}:</strong> ${name}`);
                                boundaryLayers.addLayer(layer);
                                
                                console.log(`✅ Límite cargado: ${name} (nivel ${adminLevel})`);
                            }
                        } catch (err) {
                            console.error('Error procesando elemento:', err);
                        }
                    }
                });
                
                boundaryLayers.addTo(visitaMap);
            } else {
                console.log('⚠️ No se encontraron límites administrativos en Overpass');
            }
            
        } catch (error) {
            console.error('❌ Error cargando límites:', error);
        }
    }
    

    
    function initVisitaMap() {
        const mapElement = document.getElementById('visitaMap');
        
        if (!mapElement) return;
        
        const lat = parseFloat(mapElement.dataset.lat);
        const lng = parseFloat(mapElement.dataset.lng);
        const hasCoords = mapElement.dataset.hasCoords === '1';
        
        console.log('📍 Iniciando mapa:', { lat, lng, hasCoords });
        
        if (typeof L === 'undefined') {
            setTimeout(initVisitaMap, 300);
            return;
        }
        
        try {
            // Limpiar mapa anterior
            if (visitaMap) {
                visitaMap.remove();
                visitaMap = null;
            }
            
            // Crear mapa con OpenStreetMap - centrado en Paraguay
            visitaMap = L.map('visitaMap').setView([lat, lng], hasCoords ? 16 : 6);
            
            // Tiles de OpenStreetMap con etiquetas más claras (estilo Français)
            L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 20,
                minZoom: 6
            }).addTo(visitaMap);
            
            // Restringir vista a Paraguay (-27.6, -62.6, -19.3, -54.2)
            const paraguayBounds = L.latLngBounds(
                L.latLng(-27.6, -62.6), // Suroeste
                L.latLng(-19.3, -54.2)  // Noreste
            );
            visitaMap.setMaxBounds(paraguayBounds);
            visitaMap.setMinZoom(6);
            
            // Agregar layer group para límites
            boundaryLayers.addTo(visitaMap);
            
            console.log('✅ Mapa creado');
            
            // Icono del marker
            const icon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });
            
            // Crear marker si tiene coordenadas
            if (hasCoords) {
                visitaMarker = L.marker([lat, lng], {
                    draggable: true,
                    icon: icon
                }).addTo(visitaMap);
                
                visitaMarker.bindPopup('📍 Ubicación del votante<br><small>Arrastra para mover</small>').openPopup();
                
                selectedLat = lat;
                selectedLng = lng;
                
                // Cargar límites iniciales
                loadBoundaries(lat, lng);
                
                visitaMarker.on('dragend', function(e) {
                    const position = e.target.getLatLng();
                    selectedLat = position.lat;
                    selectedLng = position.lng;
                    @this.set('latitud_seleccionada', selectedLat);
                    @this.set('longitud_seleccionada', selectedLng);
                    console.log('📍 Marker movido:', selectedLat, selectedLng);
                    visitaMarker.bindPopup(`📍 ${selectedLat.toFixed(6)}, ${selectedLng.toFixed(6)}`).openPopup();
                    loadBoundaries(selectedLat, selectedLng);
                });
            }
            
            // Click en el mapa
            visitaMap.on('click', function(e) {
                selectedLat = e.latlng.lat;
                selectedLng = e.latlng.lng;
                @this.set('latitud_seleccionada', selectedLat);
                @this.set('longitud_seleccionada', selectedLng);
                
                if (visitaMarker) {
                    visitaMarker.setLatLng(e.latlng);
                    visitaMarker.bindPopup(`📍 ${selectedLat.toFixed(6)}, ${selectedLng.toFixed(6)}`).openPopup();
                } else {
                    visitaMarker = L.marker(e.latlng, {
                        draggable: true,
                        icon: icon
                    }).addTo(visitaMap);
                    
                    visitaMarker.bindPopup(`📍 ${selectedLat.toFixed(6)}, ${selectedLng.toFixed(6)}`).openPopup();
                    
                    visitaMarker.on('dragend', function(ev) {
                        const position = ev.target.getLatLng();
                        selectedLat = position.lat;
                        selectedLng = position.lng;
                        visitaMarker.bindPopup(`📍 ${selectedLat.toFixed(6)}, ${selectedLng.toFixed(6)}`).openPopup();
                        loadBoundaries(selectedLat, selectedLng);
                    });
                }
                
                loadBoundaries(selectedLat, selectedLng);
                console.log('📍 Ubicación:', selectedLat, selectedLng);
            });
            
            console.log('🎉 Mapa OpenStreetMap inicializado');
        } catch (error) {
            console.error('❌ Error:', error);
        }
    }
    
    // Observador de cambios
    const observer = new MutationObserver(function() {
        const mapElement = document.getElementById('visitaMap');
        if (mapElement && !visitaMap) {
            setTimeout(initVisitaMap, 100);
        }
    });
    
    observer.observe(document.body, { childList: true, subtree: true });
    
    document.addEventListener('DOMContentLoaded', () => setTimeout(initVisitaMap, 500));
    window.addEventListener('load', () => setTimeout(initVisitaMap, 1000));
    
    if (typeof Livewire !== 'undefined') {
        Livewire.hook('message.processed', () => setTimeout(initVisitaMap, 200));
        
        // Escuchar evento del modal
        Livewire.on('visitaModalOpened', () => {
            console.log('🔔 Evento modal recibido, inicializando mapa...');
            setTimeout(initVisitaMap, 300);
        });
    }
</script>
@endpush
