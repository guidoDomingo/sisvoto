# 📚 DOCUMENTACIÓN DE API - Sistema de Campaña

## Guía completa de endpoints y uso de la API REST

---

## 🔗 Base URL

```
http://campana.local/api/v1
```

**Para producción, reemplazar por:**
```
https://tu-dominio.com/api/v1
```

---

## 🔐 Autenticación

Actualmente los endpoints están abiertos para facilitar desarrollo.

**Para producción**, descomentar middleware en `routes/api.php`:

```php
Route::middleware('auth:sanctum')->group(function () {
    // rutas protegidas
});
```

Usar Laravel Sanctum para autenticación con tokens.

---

## 📋 Índice de Endpoints

1. [Health Check](#1-health-check)
2. [Votantes](#2-votantes)
3. [Predicciones](#3-predicciones)
4. [Métricas](#4-métricas)
5. [Viajes](#5-viajes)
6. [Gastos](#6-gastos)
7. [Importación](#7-importación)

---

## 1. Health Check

### GET `/health`

Verificar estado del sistema.

**Request:**
```http
GET /api/v1/health
```

**Response: 200 OK**
```json
{
  "status": "ok",
  "timestamp": "2024-01-15T10:30:00Z",
  "version": "1.0.0"
}
```

---

## 2. Votantes

### GET `/votantes`

Listar votantes con filtros y paginación.

**Parámetros de Query:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `lider_id` | int | Filtrar por líder |
| `codigo_intencion` | string | A, B, C, D o E |
| `estado_contacto` | string | Nuevo, Contactado, etc. |
| `ya_voto` | boolean | true/false |
| `necesita_transporte` | boolean | true/false |
| `barrio` | string | Nombre del barrio |
| `buscar` | string | Buscar en nombre, CI, teléfono |
| `sort_by` | string | Campo para ordenar |
| `sort_dir` | string | asc o desc |
| `per_page` | int | Registros por página (default: 15) |

**Request:**
```http
GET /api/v1/votantes?lider_id=1&codigo_intencion=A&per_page=20
```

**Response: 200 OK**
```json
{
  "data": [
    {
      "id": 1,
      "ci": "1234567",
      "nombres": "Juan",
      "apellidos": "Pérez",
      "telefono": "0981-123456",
      "codigo_intencion": "A",
      "estado_contacto": "Contactado",
      "ya_voto": false,
      "necesita_transporte": true,
      "lider": {
        "id": 1,
        "territorio": "Centro - Zona 1"
      }
    }
  ],
  "current_page": 1,
  "total": 250,
  "per_page": 20
}
```

---

### POST `/votantes`

Crear nuevo votante.

**Request Body:**
```json
{
  "ci": "7654321",
  "nombres": "María",
  "apellidos": "González López",
  "telefono": "0981-654321",
  "email": "maria@email.com",
  "direccion": "Av. Principal 456",
  "barrio": "Centro",
  "zona": "Zona 1",
  "distrito": "Distrito 1",
  "lider_asignado_id": 1,
  "codigo_intencion": "B",
  "necesita_transporte": true,
  "latitud": -25.2867,
  "longitud": -57.6333
}
```

**Response: 201 Created**
```json
{
  "mensaje": "Votante creado exitosamente",
  "votante": {
    "id": 251,
    "ci": "7654321",
    "nombres": "María",
    ...
  }
}
```

**Errores: 422 Unprocessable Entity**
```json
{
  "error": "Validación fallida",
  "detalles": {
    "ci": ["El CI ya existe"],
    "nombres": ["El campo nombres es obligatorio"]
  }
}
```

---

### GET `/votantes/{id}`

Obtener votante específico con relaciones.

**Request:**
```http
GET /api/v1/votantes/1
```

**Response: 200 OK**
```json
{
  "id": 1,
  "ci": "1234567",
  "nombres": "Juan",
  "apellidos": "Pérez",
  "telefono": "0981-123456",
  "lider": {
    "id": 1,
    "territorio": "Centro - Zona 1",
    "usuario": {
      "name": "Juan Líder"
    }
  },
  "contactos": [
    {
      "id": 1,
      "contactado_en": "2024-01-10T15:30:00Z",
      "metodo": "Puerta a puerta",
      "resultado": "Exitoso",
      "usuario": {
        "name": "José Voluntario"
      }
    }
  ],
  "viajes": []
}
```

---

### PUT `/votantes/{id}`

Actualizar votante existente.

**Request Body:**
```json
{
  "codigo_intencion": "A",
  "estado_contacto": "Comprometido",
  "necesita_transporte": false
}
```

**Response: 200 OK**
```json
{
  "mensaje": "Votante actualizado exitosamente",
  "votante": { ... }
}
```

---

### PUT `/votantes/{id}/marcar-voto`

Marcar votante como "ya votó".

**Request:**
```http
PUT /api/v1/votantes/1/marcar-voto
```

**Response: 200 OK**
```json
{
  "mensaje": "Voto registrado exitosamente",
  "votante": {
    "id": 1,
    "ya_voto": true,
    "voto_registrado_en": "2024-01-15T08:45:00Z"
  }
}
```

---

### POST `/votantes/reasignar-lider`

Reasignar votantes a un nuevo líder.

**Request Body:**
```json
{
  "votante_ids": [1, 2, 3, 4, 5],
  "nuevo_lider_id": 2
}
```

**Response: 200 OK**
```json
{
  "mensaje": "Votantes reasignados exitosamente",
  "total_reasignados": 5
}
```

---

### DELETE `/votantes/{id}`

Eliminar votante (soft delete).

**Request:**
```http
DELETE /api/v1/votantes/1
```

**Response: 200 OK**
```json
{
  "mensaje": "Votante eliminado exitosamente"
}
```

---

## 3. Predicciones

### GET `/predicciones/heuristico`

Predicción usando método heurístico (probabilidades fijas).

**Parámetros opcionales:**
- `lider_id`: Filtrar por líder
- `barrio`, `zona`, `distrito`: Filtros geográficos

**Request:**
```http
GET /api/v1/predicciones/heuristico?lider_id=1
```

**Response: 200 OK**
```json
{
  "modelo": "heuristico",
  "total_votantes": 50,
  "votos_estimados": 34.5,
  "por_intencion": {
    "A": 20,
    "B": 15,
    "C": 10,
    "D": 3,
    "E": 2
  },
  "votos_estimados_por_intencion": {
    "A": 20,
    "B": 10.5,
    "C": 5,
    "D": 0.6,
    "E": 0
  },
  "porcentaje_estimado": 69.0
}
```

---

### GET `/predicciones/montecarlo`

Predicción usando simulación Monte Carlo.

**Parámetros:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `iteraciones` | int | 100-10000 (default: 1000) |
| `lider_id` | int | Opcional |

**Request:**
```http
GET /api/v1/predicciones/montecarlo?iteraciones=1000&lider_id=1
```

**Response: 200 OK**
```json
{
  "modelo": "montecarlo",
  "iteraciones": 1000,
  "total_votantes": 50,
  "estadisticas": {
    "media": 34.2,
    "mediana": 34,
    "min": 28,
    "max": 41,
    "p10": 30,
    "p90": 38,
    "desviacion_estandar": 2.5,
    "intervalo_confianza_80": [30, 38]
  },
  "histograma": [
    {
      "rango": [28, 30],
      "frecuencia": 50,
      "porcentaje": 5.0
    },
    {
      "rango": [30, 32],
      "frecuencia": 150,
      "porcentaje": 15.0
    },
    ...
  ]
}
```

---

### GET `/predicciones/combinado`

Predicción combinada (heurístico + Monte Carlo).

**Request:**
```http
GET /api/v1/predicciones/combinado?iteraciones=1000
```

**Response: 200 OK**
```json
{
  "heuristico": { ... },
  "montecarlo": { ... },
  "comparacion": {
    "diferencia_absoluta": 0.3,
    "diferencia_porcentual": 0.87
  }
}
```

---

## 4. Métricas

### GET `/metricas/generales`

Métricas generales de toda la campaña.

**Request:**
```http
GET /api/v1/metricas/generales
```

**Response: 200 OK**
```json
{
  "total_votantes": 250,
  "ya_votaron": 25,
  "pendientes_votar": 225,
  "porcentaje_votacion": 10.0,
  "contactados": 180,
  "no_contactados": 70,
  "porcentaje_contactados": 72.0,
  "necesitan_transporte": 75,
  "por_intencion": {
    "A": 100,
    "B": 75,
    "C": 50,
    "D": 15,
    "E": 10
  },
  "votos_estimados": 172.5
}
```

---

### GET `/metricas/lider/{id}`

Métricas de un líder específico.

**Request:**
```http
GET /api/v1/metricas/lider/1
```

**Response: 200 OK**
```json
{
  "lider": {
    "id": 1,
    "nombre": "Juan Líder",
    "territorio": "Centro - Zona 1",
    "meta_votos": 200
  },
  "total_votantes": 50,
  "ya_votaron": 5,
  "pendientes_votar": 45,
  "porcentaje_votacion": 10.0,
  "contactados": 40,
  "no_contactados": 10,
  "porcentaje_contactados": 80.0,
  "necesitan_transporte": 15,
  "por_intencion": { ... },
  "votos_estimados": 34.5,
  "porcentaje_meta": 17.25
}
```

---

### GET `/metricas/conversion-contactos`

Tasa de conversión de contactos.

**Response: 200 OK**
```json
{
  "total_registrados": 250,
  "contactados": 180,
  "tasa_contacto": 72.0,
  "comprometidos": 50,
  "tasa_compromiso": 27.78,
  "votos_estimados": 172.5,
  "tasa_conversion_votos": 69.0
}
```

---

### GET `/metricas/costo-por-voto`

Cálculo de costo por voto estimado.

**Response: 200 OK**
```json
{
  "total_gastado": 5000000,
  "votos_estimados": 172.5,
  "costo_por_voto": 28985.51,
  "gastos_por_categoria": {
    "Combustible": 1200000,
    "Transporte": 800000,
    "Publicidad": 2000000,
    "Eventos": 500000,
    "Otros": 500000
  }
}
```

---

### GET `/metricas/roi`

Retorno de inversión estimado.

**Parámetros:**
- `valor_por_voto` (default: 50000): Valor monetario asignado a cada voto

**Request:**
```http
GET /api/v1/metricas/roi?valor_por_voto=50000
```

**Response: 200 OK**
```json
{
  "total_gastado": 5000000,
  "votos_estimados": 172.5,
  "valor_por_voto": 50000,
  "valor_total_estimado": 8625000,
  "roi_porcentaje": 72.5,
  "beneficio_neto": 3625000
}
```

---

## 5. Viajes

### GET `/viajes`

Listar viajes con filtros.

**Parámetros:**
- `fecha`: Filtrar por fecha (YYYY-MM-DD)
- `estado`: Planificado, Confirmado, En curso, Completado, Cancelado
- `lider_id`: Filtrar por líder

**Request:**
```http
GET /api/v1/viajes?fecha=2024-01-20&estado=Planificado
```

**Response: 200 OK**
```json
{
  "data": [
    {
      "id": 1,
      "fecha_viaje": "2024-01-20",
      "hora_salida": "07:00",
      "vehiculo": {
        "placa": "ABC-123",
        "capacidad_pasajeros": 5
      },
      "chofer": {
        "nombre_completo": "Carlos Rodríguez"
      },
      "distancia_estimada_km": 15.5,
      "costo_total": 125000,
      "estado": "Planificado",
      "votantes": [ ... ]
    }
  ]
}
```

---

### POST `/viajes`

Crear nuevo viaje.

**Request Body:**
```json
{
  "vehiculo_id": 1,
  "chofer_id": 1,
  "lider_responsable_id": 1,
  "fecha_viaje": "2024-01-20",
  "hora_salida": "07:00",
  "punto_partida": "Local de campaña",
  "destino": "Centro de votación",
  "distancia_estimada_km": 15.5,
  "viaticos": 20000,
  "votantes": [1, 2, 3, 4, 5]
}
```

**Response: 201 Created**
```json
{
  "mensaje": "Viaje creado exitosamente",
  "viaje": { ... }
}
```

---

### POST `/viajes/generar-plan`

Generar plan automático de viajes para un líder.

**Request Body:**
```json
{
  "lider_id": 1,
  "fecha": "2024-01-20"
}
```

**Response: 200 OK**
```json
{
  "fecha": "2024-01-20",
  "total_votantes": 30,
  "viajes_necesarios": 6,
  "vehiculo_sugerido": {
    "id": 1,
    "placa": "ABC-123",
    "capacidad": 5
  },
  "grupos": [
    {
      "numero_viaje": 1,
      "chofer": {
        "id": 1,
        "nombre": "Carlos Rodríguez"
      },
      "votantes": [ ... ],
      "num_pasajeros": 5,
      "distancia_estimada_km": 12.5,
      "costo_estimado": 105000
    },
    ...
  ],
  "costo_total_estimado": 630000
}
```

---

## 6. Gastos

### GET `/gastos`

Listar gastos con filtros.

**Parámetros:**
- `categoria`: Combustible, Transporte, Publicidad, etc.
- `aprobado`: true/false
- `fecha_desde`, `fecha_hasta`: Rango de fechas

**Request:**
```http
GET /api/v1/gastos?categoria=Combustible&aprobado=true
```

**Response: 200 OK**
```json
{
  "data": [
    {
      "id": 1,
      "categoria": "Combustible",
      "descripcion": "Carga de combustible vehículo ABC-123",
      "monto": 250000,
      "fecha_gasto": "2024-01-15",
      "aprobado": true,
      "usuario_registro": {
        "name": "Juan Líder"
      }
    }
  ]
}
```

---

### POST `/gastos`

Registrar nuevo gasto.

**Request Body:**
```json
{
  "categoria": "Combustible",
  "descripcion": "Carga de combustible",
  "monto": 250000,
  "fecha_gasto": "2024-01-15",
  "numero_recibo": "R-001234",
  "proveedor": "Estación Petrobras",
  "notas": "Para viaje del 20/01"
}
```

**Response: 201 Created**
```json
{
  "mensaje": "Gasto registrado exitosamente",
  "gasto": { ... }
}
```

---

### PUT `/gastos/{id}/aprobar`

Aprobar un gasto.

**Request:**
```http
PUT /api/v1/gastos/1/aprobar
```

**Response: 200 OK**
```json
{
  "mensaje": "Gasto aprobado exitosamente",
  "gasto": {
    "id": 1,
    "aprobado": true,
    "aprobado_en": "2024-01-15T10:30:00Z"
  }
}
```

---

### GET `/gastos/resumen/por-categoria`

Resumen de gastos agrupados por categoría.

**Parámetros:**
- `fecha_desde`, `fecha_hasta`: Rango de fechas

**Request:**
```http
GET /api/v1/gastos/resumen/por-categoria?fecha_desde=2024-01-01&fecha_hasta=2024-01-31
```

**Response: 200 OK**
```json
{
  "resumen": [
    {
      "categoria": "Combustible",
      "cantidad": 15,
      "total": 1200000
    },
    {
      "categoria": "Publicidad",
      "cantidad": 8,
      "total": 2000000
    }
  ],
  "total_general": 5000000
}
```

---

## 7. Importación

### POST `/importacion/votantes`

Importar votantes desde archivo CSV o XLSX.

**Request:**
```http
POST /api/v1/importacion/votantes
Content-Type: multipart/form-data

archivo: [archivo.csv]
lider_asignado_id: 1
actualizar_duplicados: false
```

**Formato CSV esperado:**
```csv
ci,nombres,apellidos,telefono,email,direccion,barrio,codigo_intencion,necesita_transporte
1234567,Juan,Pérez,0981-123456,juan@email.com,Av. Principal 123,Centro,A,Si
```

**Response: 200 OK**
```json
{
  "exito": true,
  "total_procesados": 100,
  "nuevos": 95,
  "actualizados": 0,
  "duplicados": 3,
  "fallidos": 2,
  "errores": [
    {
      "fila": 45,
      "errores": ["El campo nombres es obligatorio"]
    }
  ],
  "advertencias": [
    {
      "fila": 23,
      "mensaje": "Votante duplicado (CI: 1234567)"
    }
  ]
}
```

---

### GET `/importacion/plantilla`

Descargar plantilla CSV de ejemplo.

**Request:**
```http
GET /api/v1/importacion/plantilla
```

**Response: 200 OK**
```
Content-Type: text/csv
Content-Disposition: attachment; filename="plantilla_votantes.csv"

ci,nombres,apellidos,telefono,...
1234567,Juan,Pérez García,0981-123456,...
```

---

## 📝 Notas Importantes

### Códigos de Intención de Voto

- **A**: Voto seguro (probabilidad: 1.0)
- **B**: Probable (probabilidad: 0.7)
- **C**: Indeciso (probabilidad: 0.5)
- **D**: Difícil (probabilidad: 0.2)
- **E**: Contrario (probabilidad: 0.0)

### Estados de Contacto

- Nuevo
- Contactado
- Re-contacto
- Comprometido
- Crítico

### Estados de Viajes

- Planificado
- Confirmado
- En curso
- Completado
- Cancelado

---

## 🧪 Ejemplos con cURL

### Listar votantes

```bash
curl -X GET "http://campana.local/api/v1/votantes?per_page=10"
```

### Crear votante

```bash
curl -X POST "http://campana.local/api/v1/votantes" \
  -H "Content-Type: application/json" \
  -d '{
    "nombres": "Test",
    "apellidos": "Usuario",
    "lider_asignado_id": 1,
    "codigo_intencion": "A"
  }'
```

### Obtener predicción

```bash
curl -X GET "http://campana.local/api/v1/predicciones/montecarlo?iteraciones=1000"
```

---

## 🔒 Seguridad en Producción

**Antes de desplegar:**

1. ✅ Activar `auth:sanctum` middleware
2. ✅ Cambiar `APP_DEBUG=false`
3. ✅ Configurar CORS
4. ✅ Implementar rate limiting
5. ✅ Usar HTTPS
6. ✅ Validar y sanitizar inputs
7. ✅ Implementar logs de auditoría

---

**Documentación actualizada: Enero 2024**
