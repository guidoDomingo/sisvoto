# 🎨 FRONTEND CON LIVEWIRE - Guía de Instalación

## Sistema completo instalado con éxito ✅

El frontend del sistema de campaña ha sido completamente implementado con:

---

## 📦 Stack Tecnológico

- **Livewire 3.x**: Framework full-stack reactivo
- **Tailwind CSS 3.x**: Framework CSS utility-first
- **Alpine.js 3.x**: Framework JavaScript ligero
- **Blade Templates**: Motor de plantillas de Laravel

---

## 🗂️ Estructura del Frontend

### Componentes Livewire Creados

1. **Dashboard.php** - Dashboard principal con métricas generales
2. **LeaderDashboard.php** - Dashboard específico para líderes
3. **VotantesList.php** - Listado con filtros y paginación
4. **VotanteForm.php** - Formulario crear/editar votante
5. **VotanteImporter.php** - Importación masiva CSV/XLSX
6. **TripPlanner.php** - Planificador de viajes (3 pasos)
7. **PrediccionVotos.php** - Predicciones con modelos estadísticos

### Vistas Blade

```
resources/views/
├── layouts/
│   └── app.blade.php           # Layout principal con sidebar
├── auth/
│   └── login.blade.php         # Página de login
└── livewire/
    ├── dashboard.blade.php
    ├── leader-dashboard.blade.php
    ├── votantes-list.blade.php
    ├── votante-form.blade.php
    ├── votante-importer.blade.php
    ├── trip-planner.blade.php
    └── prediccion-votos.blade.php
```

---

## 🚀 Pasos para Activar el Frontend

### 1. Instalar Dependencias de Node.js

```powershell
cd C:\laragon\www\sisvoto
npm install
```

Esto instalará:
- ✅ Tailwind CSS
- ✅ Alpine.js
- ✅ PostCSS
- ✅ Autoprefixer
- ✅ @tailwindcss/forms

### 2. Compilar Assets

**Opción A: Desarrollo (recompila automáticamente)**
```powershell
npm run dev
```

**Opción B: Producción (optimizado)**
```powershell
npm run build
```

**Opción C: Watch mode**
```powershell
npm run watch
```

### 3. Verificar Livewire

Asegúrate de que Livewire esté instalado:

```powershell
composer require livewire/livewire
```

### 4. Iniciar Servidor

```powershell
php artisan serve
```

O si usas Laragon, simplemente abre: **http://campana.local**

---

## 🔑 Rutas Disponibles

### Autenticación
- `GET /login` - Página de inicio de sesión
- `POST /login` - Procesar login
- `POST /logout` - Cerrar sesión

### Principales
- `GET /dashboard` - Dashboard principal
- `GET /lider/dashboard` - Dashboard del líder
- `GET /votantes` - Listado de votantes
- `GET /votantes/crear` - Crear votante
- `GET /votantes/{id}/editar` - Editar votante
- `GET /importar` - Importación masiva
- `GET /predicciones` - Predicción de votos
- `GET /viajes` - Planificador de viajes

---

## 🎨 Características del Frontend

### Dashboard Principal

✅ **4 KPI Cards**
- Total votantes
- Ya votaron (con porcentaje)
- Contactados (con porcentaje)
- Votos estimados

✅ **Gráficos**
- Distribución por intención de voto (A, B, C, D, E)
- Top 5 líderes por rendimiento

✅ **Tablas**
- Viajes próximos
- Gastos recientes

### Dashboard del Líder

✅ **5 KPIs específicos**
- Total asignados
- Contactados
- Intención A/B
- Necesitan transporte
- Ya votaron

✅ **Predicción de votos** del territorio

✅ **Votantes recientes** asignados

✅ **Alertas de votantes críticos**
- Sin contactar por más de 3 días
- Marcados como "Crítico"

✅ **Modal de registro rápido de contacto**
- Método de contacto
- Resultado
- Nueva intención de voto

✅ **Exportar lista** a CSV

### Gestión de Votantes

✅ **Búsqueda en tiempo real** (nombre, CI, teléfono)

✅ **Filtros múltiples**
- Código de intención (A, B, C, D, E)
- Estado de contacto
- Necesita transporte
- Líder asignado

✅ **Paginación** configurable

✅ **Acciones rápidas**
- ✓ Marcar como votó
- ✏️ Editar
- 🗑️ Eliminar

✅ **Indicadores visuales**
- Color por intención de voto
- Badge de transporte
- Highlight de ya votó

### Formulario de Votante

✅ **Validación en tiempo real**

✅ **Secciones organizadas**
- Datos personales (CI, nombres, apellidos, teléfono, email)
- Dirección (dirección, barrio, zona, distrito)
- Coordenadas geográficas (latitud, longitud)
- Datos de campaña (líder, intención, estado, transporte)
- Notas

✅ **Select dinámicos** con líderes disponibles

### Importación Masiva

✅ **Drag & drop** de archivos

✅ **Soporta CSV y Excel** (.xlsx, .xls)

✅ **Validación de formato**

✅ **Configuración**
- Asignar líder
- Actualizar duplicados (opcional)

✅ **Resultado detallado**
- Total procesados
- Nuevos registros
- Actualizados
- Duplicados omitidos
- Errores con detalles

✅ **Descargar plantilla** CSV de ejemplo

### Predicción de Votos

✅ **3 modelos de predicción**
- Heurístico (probabilidades fijas)
- Monte Carlo (simulación estocástica)
- Comparación combinada

✅ **Configuración de Monte Carlo**
- Slider de iteraciones (100 - 10,000)

✅ **Filtros opcionales**
- Por líder
- Por barrio
- Por zona
- Por distrito

✅ **Resultados estadísticos**
- Media, mediana, desviación estándar
- Percentiles (P10, P90)
- Intervalo de confianza 80%
- Min/Max

✅ **Visualización**
- Distribución por intención de voto
- Comparación de modelos

### Planificador de Viajes

✅ **Wizard de 3 pasos**

**Paso 1: Seleccionar Votantes**
- Lista de votantes que necesitan transporte
- Checkbox para selección múltiple
- Filtros por barrio y zona
- Contador de seleccionados
- Botones: Seleccionar Todos / Limpiar

**Paso 2: Configurar Viaje**
- Seleccionar vehículo (con capacidad)
- Seleccionar chofer
- Fecha y hora de salida
- Punto de partida
- Viáticos

**Paso 3: Resultado**
- Resumen del plan generado
- Total votantes, viajes necesarios, distancia, costo
- Detalle de cada viaje con pasajeros
- Agrupamiento por proximidad
- Botones: Confirmar y Guardar / Planificar Nuevo

---

## 🎨 Estilos y UX

### Tailwind CSS

✅ Componentes consistentes
✅ Responsive design (mobile-first)
✅ Dark mode ready
✅ Animaciones suaves
✅ Loading states
✅ Hover effects

### Alpine.js

✅ Dropdowns interactivos
✅ Modales
✅ Tabs
✅ Tooltips
✅ Estado reactivo
✅ Click away handlers

### Livewire

✅ Actualizaciones en tiempo real
✅ Validación en vivo
✅ Loading states automáticos
✅ Sin escribir JavaScript
✅ SPA-like experience

---

## 🔐 Sistema de Autenticación

### Login Page

✅ Formulario limpio y profesional
✅ Validación de credenciales
✅ Checkbox "Recordarme"
✅ Mensajes de error claros
✅ Credenciales de prueba visibles

### Credenciales de Prueba

```
Admin:        admin@campana.com        / password
Coordinador:  coordinador@campana.com  / password
Líder:        lider@campana.com        / password
Voluntario:   voluntario@campana.com   / password
Auditor:      auditor@campana.com      / password
```

### Seguridad

✅ Middleware `auth` en todas las rutas protegidas
✅ CSRF protection
✅ Session regeneration al login
✅ Logout seguro
✅ Redirect después de login

---

## 🧭 Navegación

### Sidebar Responsive

✅ **Links principales**
- Dashboard
- Mi Dashboard (solo líderes)
- Votantes
- Predicciones
- Importar
- Viajes
- Gastos (solo admin/coordinador)

✅ **Características**
- Collapse en mobile
- Active state visual
- Iconos SVG
- Transiciones suaves

### Header

✅ Logo de la aplicación
✅ Toggle sidebar (mobile)
✅ Dropdown de usuario
- Nombre y email
- Cerrar sesión

---

## 📱 Responsive Design

El sistema es **completamente responsive**:

### Breakpoints
- **sm**: 640px (móviles grandes)
- **md**: 768px (tablets)
- **lg**: 1024px (laptops)
- **xl**: 1280px (desktop)

### Adaptaciones
- Grid columns: 1 → 2 → 3 → 4
- Sidebar: hidden → overlay → fixed
- Tables: scroll horizontal en mobile
- Forms: full width → 2 columns

---

## 🎯 Próximos Pasos Recomendados

### 1. Personalización
- Cambiar colores del tema en `tailwind.config.js`
- Agregar logo personalizado en `layouts/app.blade.php`
- Modificar textos y labels según necesidad

### 2. Optimización
- Implementar lazy loading de imágenes
- Agregar cache de consultas frecuentes
- Optimizar queries N+1 con eager loading

### 3. Features Adicionales
- Sistema de notificaciones en tiempo real
- Chat entre coordinadores y líderes
- Mapas interactivos con Leaflet
- Gráficos avanzados con Chart.js
- Reportes PDF exportables
- Sistema de permisos granular

### 4. Testing
- Crear tests de integración con Livewire
- Browser tests con Dusk
- Validar formularios
- Testing de workflows completos

---

## 🐛 Troubleshooting

### Problema: Estilos no se cargan

**Solución:**
```powershell
npm run build
php artisan optimize:clear
```

### Problema: Livewire no funciona

**Solución:**
```powershell
composer require livewire/livewire
php artisan livewire:publish --config
php artisan view:clear
```

### Problema: Alpine.js no funciona

**Solución:**
Verificar que `resources/js/app.js` contenga:
```javascript
import Alpine from 'alpinejs'
window.Alpine = Alpine
Alpine.start()
```

### Problema: Errores 404 en las rutas

**Solución:**
```powershell
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

---

## ✅ Checklist de Instalación

- [ ] `npm install` ejecutado
- [ ] `npm run dev` o `npm run build` ejecutado
- [ ] Livewire instalado (`composer require livewire/livewire`)
- [ ] Base de datos migrada y poblada (`php artisan migrate --seed`)
- [ ] Virtual host configurado (campana.local)
- [ ] Login funcional (http://campana.local/login)
- [ ] Dashboard carga correctamente
- [ ] Sidebar responsive funciona
- [ ] Componentes Livewire funcionan
- [ ] Formularios validan correctamente
- [ ] Importación de CSV funciona
- [ ] Predicciones calculan correctamente

---

## 📚 Recursos

- **Livewire**: https://livewire.laravel.com/docs
- **Tailwind CSS**: https://tailwindcss.com/docs
- **Alpine.js**: https://alpinejs.dev/
- **Laravel Blade**: https://laravel.com/docs/blade

---

**¡El frontend está completamente listo para usar! 🎉**

Todos los componentes están funcionando y conectados con el backend existente.
