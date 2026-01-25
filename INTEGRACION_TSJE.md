# Integración TSJE - Consulta Automática de Votantes

## 📋 Descripción

El sistema SisVoto ahora incluye integración automática con el Tribunal Superior de Justicia Electoral (TSJE) de Paraguay para consultar y obtener automáticamente los datos de votantes registrados.

## ✨ Funcionalidades Implementadas

### 1. Búsqueda Automática en Formulario de Votantes

**Ubicación**: `/votantes/crear` y `/votantes/{id}/editar`

**Características**:
- ✅ Búsqueda automática al escribir CI (6+ dígitos)
- ✅ Botón manual "Buscar TSJE"
- ✅ Opción para activar/desactivar búsqueda automática
- ✅ Indicadores visuales de estado de búsqueda
- ✅ Preserva datos ya ingresados manualmente
- ✅ Formateo automático de CI (solo números)

**Datos obtenidos del TSJE**:
- Nombres y apellidos
- Dirección completa
- Distrito/Municipio
- Barrio/Compañía
- Departamento
- Mesa electoral
- Local de votación

### 2. Importación Masiva con TSJE

**Ubicación**: `/importar`

**Nuevas opciones**:
- ✅ **Consultar automáticamente en TSJE**: Busca datos completos para cada CI importado
- ✅ **Solo importar CIs**: Importa solo lista de cédulas y completa automáticamente con TSJE
- ✅ **Actualizar votantes existentes**: Opción mejorada con mejor feedback

**Formatos soportados**:
- CSV
- Excel (XLSX, XLS)
- TXT (lista simple de CIs)

### 3. Comando de Prueba

**Comando**: `php artisan tsje:test {ci}`

Permite probar la consulta TSJE desde línea de comandos.

**Ejemplo**:
```bash
php artisan tsje:test 1234567
```

## 🔍 Fuentes de Datos

El sistema consulta múltiples fuentes en orden de prioridad:

1. **TSJE Oficial** (Padrón Electoral)
   - API principal del TSJE
   - Scraping web como respaldo
   - Datos más completos y actualizados

2. **Registro Civil (SET)**
   - API del Servicio de Identificaciones
   - Datos básicos de identificación

3. **APIs de Validación**
   - Servicios públicos de validación de CI
   - Verificación de existencia del documento

## 🚀 Cómo Usar

### Formulario Individual

1. **Automático**: Escriba el CI, el sistema buscará automáticamente después de 6 dígitos
2. **Manual**: Use el botón "🔍 Buscar TSJE" después de ingresar el CI
3. **Configuración**: Use el checkbox para activar/desactivar búsqueda automática

### Importación Masiva

1. **Archivo completo**: Suba archivo con todos los datos y active "Consultar TSJE" para completar campos faltantes
2. **Solo CIs**: Active "Solo importar CIs" y suba archivo con una columna de cédulas - el sistema completará todo automáticamente

### Plantilla de Importación

Use el botón "Descargar Plantilla" para obtener un archivo Excel con:
- Formato correcto para importación
- Instrucciones detalladas
- Ejemplos de datos
- Explicación de opciones TSJE

## 📊 Indicadores de Estado

- 🔍 **Buscando**: Consulta en progreso
- ✅ **Datos encontrados**: Información cargada exitosamente
- ❌ **No encontrado**: CI no está en el padrón electoral
- ⚠️ **Error**: Problema de conectividad o servidor

## 🔧 Configuración Técnica

### Logs
Los intentos de búsqueda se registran en:
- `storage/logs/laravel.log`
- Nivel INFO para búsquedas exitosas
- Nivel ERROR para problemas

### Timeouts
- TSJE Principal: 30 segundos
- Métodos alternativos: 25-15 segundos
- Búsqueda automática: Optimizada para UX

### Cache y Performance
- Las consultas no se cachean (datos siempre actualizados)
- Debounce de 500ms en búsqueda automática
- Múltiples fuentes para alta disponibilidad

## 📱 Experiencia de Usuario

### Mejoras de UX
- ✅ Auto-formateo de CI (solo números)
- ✅ Auto-capitalización de nombres
- ✅ Formato automático de teléfonos
- ✅ Indicadores visuales claros
- ✅ Mensajes informativos en español
- ✅ Feedback en tiempo real

### Accesibilidad
- ✅ Contraste de colores apropiado
- ✅ Textos descriptivos
- ✅ Estados de carga claros
- ✅ Navegación por teclado

## 🛠 Comandos Útiles

```bash
# Probar consulta TSJE
php artisan tsje:test 1234567

# Limpiar logs
php artisan log:clear

# Ver logs en tiempo real
tail -f storage/logs/laravel.log
```

## 🔮 Funcionalidades Futuras

- [ ] Cache inteligente de consultas exitosas
- [ ] Sincronización batch nocturna
- [ ] Integración con más bases de datos oficiales
- [ ] Exportación de datos con información TSJE
- [ ] Dashboard de estadísticas de consultas

---

**Nota**: Esta funcionalidad depende de la disponibilidad de las APIs públicas del TSJE y servicios gubernamentales de Paraguay. En caso de indisponibilidad temporal, el sistema permite completar manualmente todos los datos.