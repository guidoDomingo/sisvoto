# 🚀 GUÍA RÁPIDA DE USO - Sistema de Campaña

## Acceso al Sistema

1. **Abrir navegador**: http://campana.local/login

2. **Credenciales de prueba**:
   - **Admin**: admin@campana.com / password
   - **Coordinador**: coordinador@campana.com / password
   - **Líder**: lider@campana.com / password

---

## 📊 Dashboard Principal

Al iniciar sesión verás:

### Métricas Principales
- **Total Votantes**: 250 (del seeder)
- **Ya Votaron**: Cantidad y porcentaje
- **Contactados**: Cantidad y porcentaje
- **Votos Estimados**: Predicción heurística

### Gráficos
- **Intención de Voto**: Distribución A, B, C, D, E
- **Top Líderes**: Los 5 líderes con más votantes

### Tablas
- **Viajes Próximos**: Próximos 5 viajes planificados
- **Gastos Recientes**: Últimos 5 gastos registrados

---

## 👤 Dashboard del Líder

Si inicias sesión como líder (`lider@campana.com`):

### KPIs Personales
- Total asignados a ti
- Contactados por ti
- Intención A/B (seguros + probables)
- Necesitan transporte
- Ya votaron

### Acciones Rápidas
1. **Votantes Recientes**: Ver tus últimos 5 registros
2. **Alertas Críticas**: Votantes que necesitan atención urgente
3. **Registrar Contacto**: Modal rápido para guardar interacción
4. **Marcar Voto**: Botón rápido para confirmar que votó
5. **Exportar Lista**: Descargar CSV con todos tus votantes

---

## 📋 Gestión de Votantes

**Ruta**: `/votantes`

### Búsqueda y Filtros

1. **Barra de búsqueda**: Busca por nombre, CI o teléfono
2. **Filtros disponibles**:
   - Intención de voto (A, B, C, D, E)
   - Estado de contacto (Nuevo, Contactado, etc.)
   - Necesita transporte (Sí/No)

### Acciones en la Lista

- ✓ **Marcar voto**: Click en el check verde
- ✏️ **Editar**: Click en el lápiz azul
- 🗑️ **Eliminar**: Click en el tacho rojo

### Crear Nuevo Votante

1. Click en **"Nuevo Votante"**
2. Completar formulario:
   - **Obligatorios**: CI, Nombres, Apellidos, Líder, Intención
   - **Opcionales**: Todo lo demás
3. Click en **"Guardar Votante"**

### Editar Votante

1. Click en el ícono de lápiz en la tabla
2. Modificar campos necesarios
3. Click en **"Actualizar Votante"**

---

## 📥 Importación Masiva

**Ruta**: `/importar`

### Paso a Paso

1. **Descargar plantilla**: Click en "Descargar Plantilla CSV"

2. **Completar Excel/CSV**:
   ```csv
   ci,nombres,apellidos,telefono,email,direccion,barrio,codigo_intencion,necesita_transporte
   1234567,Juan,Pérez,0981-123456,juan@mail.com,Av. Test 123,Centro,A,Si
   ```

3. **Subir archivo**:
   - Drag & drop o click para seleccionar
   - Formatos: CSV, XLSX, XLS (máx 10MB)

4. **Configurar**:
   - Seleccionar líder para asignar
   - Marcar "Actualizar duplicados" si deseas (opcional)

5. **Importar**: Click en "Importar Votantes"

6. **Ver resultado**:
   - Total procesados
   - Nuevos registros creados
   - Duplicados omitidos
   - Errores (si hubo)

---

## 📈 Predicción de Votos

**Ruta**: `/predicciones`

### Modelos Disponibles

#### 1. Heurístico (Rápido)
- Usa probabilidades fijas:
  - A = 100% (voto seguro)
  - B = 70% (probable)
  - C = 50% (indeciso)
  - D = 20% (difícil)
  - E = 0% (contrario)

#### 2. Monte Carlo (Preciso)
- Simulación estocástica
- Configurable: 100 a 10,000 iteraciones
- Resultados:
  - Media, mediana
  - Desviación estándar
  - Percentiles (P10, P90)
  - Intervalo de confianza 80%

#### 3. Comparación Combinada
- Ejecuta ambos modelos
- Muestra diferencia entre ellos
- Recomendado para tomar decisiones

### Filtros Opcionales

- Por líder específico
- Por barrio
- Por zona
- Por distrito

### Cómo Usar

1. Seleccionar modelo
2. Configurar iteraciones (si es Monte Carlo)
3. Aplicar filtros (opcional)
4. Click en **"Calcular"**
5. Analizar resultados

---

## 🚗 Planificador de Viajes

**Ruta**: `/viajes`

### Paso 1: Seleccionar Votantes

1. Aparecen todos los votantes que:
   - Necesitan transporte ✓
   - Aún no votaron ✓
   - Tienen coordenadas geográficas ✓

2. **Filtrar** por barrio o zona

3. **Seleccionar votantes**:
   - Individualmente: Click en checkbox
   - Todos: Click en "Seleccionar Todos"
   - Limpiar: Click en "Limpiar"

4. Click en **"Continuar"**

### Paso 2: Configurar Viaje

Completar:
- **Vehículo**: Seleccionar de la lista disponible (muestra capacidad)
- **Chofer**: Seleccionar conductor disponible
- **Fecha**: Día del viaje
- **Hora de salida**: Formato 24hs
- **Punto de partida**: Ej: "Local de campaña"
- **Viáticos**: Monto en guaraníes (default: 20,000)

Click en **"Generar Plan"**

### Paso 3: Resultado

El sistema automáticamente:
- ✓ Agrupa votantes por proximidad geográfica
- ✓ Calcula viajes necesarios según capacidad del vehículo
- ✓ Estima distancia por viaje
- ✓ Calcula costo total (combustible + chofer + viáticos)

**Resumen muestra**:
- Total votantes
- Viajes necesarios
- Distancia total
- Costo total estimado

**Detalle por viaje**:
- Lista de pasajeros
- Distancia estimada
- Costo del viaje

**Acciones**:
- **Confirmar y Guardar**: Crea los viajes en la BD
- **Planificar Nuevo**: Reinicia el wizard

---

## 💡 Tips y Mejores Prácticas

### Para Líderes

1. **Contacta regularmente**:
   - Usa el dashboard para ver alertas críticas
   - Registra cada contacto con el modal rápido
   - Actualiza la intención de voto después de cada interacción

2. **Monitorea tu territorio**:
   - Revisa tus KPIs diariamente
   - Identifica votantes sin contactar
   - Prioriza intenciones C, D (indecisos/difíciles)

3. **Exporta listas**:
   - Usa el botón "Exportar Lista" para backup
   - Comparte con voluntarios
   - Imprime para trabajo en campo

### Para Coordinadores/Admin

1. **Importa masivamente**:
   - Usa la plantilla CSV para cargar muchos votantes
   - Valida el archivo antes de subir
   - Revisa errores después de importar

2. **Analiza predicciones**:
   - Ejecuta Monte Carlo con 1,000+ iteraciones
   - Compara con heurístico
   - Filtra por líder para evaluar territorios

3. **Optimiza transporte**:
   - Planifica viajes con anticipación
   - Agrupa por barrio/zona
   - Revisa costos antes de confirmar

---

## 🔍 Búsquedas Rápidas

### Encontrar un votante específico

1. Ir a `/votantes`
2. Escribir en barra de búsqueda: CI, nombre o teléfono
3. Resultados filtran en tiempo real

### Ver votantes de un líder

1. Ir a `/votantes`
2. Seleccionar líder en el filtro
3. Click en "Aplicar"

### Votantes que necesitan transporte

1. Ir a `/votantes`
2. Filtro "Necesita transporte" → Sí
3. Ver lista completa

---

## ⚠️ Problemas Comunes

### "No veo votantes en mi lista"

**Causa**: Eres líder y aún no tienes votantes asignados

**Solución**: Pide a un coordinador que te asigne votantes o crea algunos nuevos

### "La importación falló"

**Causas posibles**:
- Formato incorrecto del CSV
- CI duplicado
- Campos obligatorios vacíos

**Solución**: Revisa el resultado, corrige errores en el archivo y vuelve a importar

### "No puedo generar plan de viajes"

**Causas posibles**:
- Ningún votante tiene coordenadas geográficas
- No hay vehículos/choferes disponibles

**Solución**: 
- Edita votantes para agregar latitud/longitud
- Verifica disponibilidad de vehículos en la BD

### "Los estilos se ven rotos"

**Solución**:
```powershell
npm run build
php artisan optimize:clear
# Refrescar navegador (Ctrl + F5)
```

---

## 🎯 Flujo de Trabajo Recomendado

### Día de Elección

1. **Mañana temprano**:
   - Coordinadores: Revisar viajes planificados
   - Líderes: Abrir dashboard, ver votantes pendientes

2. **Durante el día**:
   - Marcar votos en tiempo real
   - Usar móvil para actualizar desde el local
   - Registrar contactos de votantes que no llegaron

3. **Tarde**:
   - Llamar a votantes pendientes
   - Organizar viajes adicionales si es necesario
   - Monitorear predicción en tiempo real

4. **Cierre**:
   - Exportar listas finales
   - Verificar totales
   - Generar reportes

---

## 📞 Soporte

Si necesitas ayuda:

1. Revisa `INSTALACION.md` para problemas técnicos
2. Revisa `API_DOCUMENTATION.md` si trabajas con la API
3. Revisa `FRONTEND_SETUP.md` para problemas de frontend

---

**¡El sistema está listo para gestionar tu campaña de manera profesional! 🗳️**
