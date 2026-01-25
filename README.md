# 🗳️ Sistema de Gestión de Campaña Política

## Laravel 10 + Livewire + MySQL para equipos de campaña electoral

Plataforma profesional y responsiva para gestionar votantes, líderes, equipos, transporte, finanzas y predicción de votos.

---

## 📋 Tabla de Contenidos

1. [Requisitos](#requisitos)
2. [Instalación en Laragon](#instalación-en-laragon)
3. [Configuración](#configuración)
4. [Arquitectura del Sistema](#arquitectura-del-sistema)
5. [Estructura de la Base de Datos](#estructura-de-la-base-de-datos)
6. [Funcionalidades Principales](#funcionalidades-principales)
7. [API Endpoints](#api-endpoints)
8. [Testing](#testing)
9. [Consideraciones Legales](#consideraciones-legales)

---

## 🔧 Requisitos

- **Laragon Full** (última versión) - [Descargar aquí](https://laragon.org/download/)
- PHP 8.1 o superior (incluido en Laragon)
- MySQL 5.7+ (incluido en Laragon)
- Composer (incluido en Laragon)
- Node.js 18+ y npm/yarn
- Git

---

## 🚀 Instalación en Laragon

### Paso 1: Preparar Laragon

1. **Instalar Laragon Full**
   - Descarga e instala Laragon Full desde [laragon.org](https://laragon.org/download/)
   - Ejecuta Laragon y verifica que Apache y MySQL estén corriendo (Start All)

2. **Verificar versiones**
   ```powershell
   # Abre el terminal de Laragon (clic derecho en Laragon > Terminal)
   php -v          # Debe ser 8.1 o superior
   composer -V     # Composer instalado
   mysql --version # MySQL instalado
   node -v         # Node.js (si no está, instala desde nodejs.org)
   npm -v          # npm instalado
   ```

### Paso 2: Crear la Base de Datos

1. **Abrir terminal de Laragon** (clic derecho en icono Laragon > Terminal)

2. **Crear base de datos**
   ```powershell
   mysql -u root -e "CREATE DATABASE IF NOT EXISTS campana CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   ```

3. **Verificar creación** (opcional)
   ```powershell
   mysql -u root -e "SHOW DATABASES;"
   ```

### Paso 3: Clonar y Configurar el Proyecto

1. **Navegar a la carpeta www de Laragon**
   ```powershell
   cd C:\laragon\www
   ```

2. **Si es un repositorio Git (clonarlo)**
   ```powershell
   git clone <URL_REPOSITORIO> sisvoto
   cd sisvoto
   ```

3. **Si es proyecto local (ya está en sisvoto)**
   ```powershell
   cd sisvoto
   ```

### Paso 4: Instalar Dependencias

1. **Copiar archivo de configuración**
   ```powershell
   copy .env.example .env
   ```

2. **Instalar dependencias PHP**
   ```powershell
   composer install
   ```

3. **Instalar dependencias Node.js**
   ```powershell
   npm install
   ```

4. **Generar clave de aplicación**
   ```powershell
   php artisan key:generate
   ```

### Paso 5: Configurar Base de Datos

Edita el archivo `.env` con los datos de tu Laragon:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=campana
DB_USERNAME=root
DB_PASSWORD=
```

### Paso 6: Migrar y Poblar la Base de Datos

```powershell
# Ejecutar migraciones
php artisan migrate

# Poblar con datos de ejemplo (200+ votantes, líderes, vehículos, etc.)
php artisan db:seed

# O todo junto (resetea la DB):
php artisan migrate:fresh --seed
```

### Paso 7: Compilar Assets Frontend

```powershell
# Desarrollo (con hot reload)
npm run dev

# O en otra terminal, para compilar y vigilar cambios:
npm run watch

# Para producción:
npm run build
```

### Paso 8: Crear Virtual Host en Laragon

1. **Clic derecho en Laragon > Apache > sites > Add VirtualHost**
   - **Name:** `campana.local`
   - **Path:** `C:\laragon\www\sisvoto\public`

2. **Laragon agregará automáticamente la entrada al archivo hosts**

3. **Reiniciar servicios** (Stop All > Start All)

4. **Abrir navegador**: http://campana.local

### Paso 9: Verificar Instalación

Visita: http://campana.local

Deberías ver la página de inicio del sistema.

**Usuarios de prueba creados por el seeder:**

| Email | Password | Rol |
|-------|----------|-----|
| admin@campana.com | password | Super Admin |
| coordinador@campana.com | password | Coordinador |
| lider@campana.com | password | Líder |
| voluntario@campana.com | password | Voluntario |

---

## ⚙️ Configuración

### Variables de Entorno Importantes

```env
# Aplicación
APP_NAME="Sistema Campaña"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://campana.local

# Base de datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=campana
DB_USERNAME=root
DB_PASSWORD=

# Configuraciones de campaña
PRECIO_COMBUSTIBLE=7500
MAPBOX_KEY=tu_clave_api_opcional

# Email (opcional, para notificaciones)
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
```

### Comandos Útiles

```powershell
# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Crear usuario manualmente
php artisan tinker
>>> User::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => bcrypt('password')]);

# Ver rutas
php artisan route:list

# Ejecutar tests
php artisan test

# Queue worker (para tareas asíncronas)
php artisan queue:work
```

---

## 🏗️ Arquitectura del Sistema

### Jerarquía Organizacional

```
Coordinador General
    ├── Coordinador de Zona 1
    │   ├── Líder 1 (territorio A)
    │   │   ├── Voluntario 1
    │   │   ├── Voluntario 2
    │   │   └── Votantes asignados (50-200)
    │   └── Líder 2 (territorio B)
    └── Coordinador de Zona 2
        └── Líder 3...
```

### Módulos Principales

1. **Gestión de Votantes**: Registro, seguimiento, contactos
2. **Organización de Equipos**: Líderes, voluntarios, territorios
3. **Logística**: Vehículos, choferes, planificación de viajes
4. **Finanzas**: Gastos, presupuestos, ROI
5. **Predicción**: Algoritmos heurísticos y Monte Carlo
6. **Reportes**: Dashboards en tiempo real, métricas clave

---

## 💾 Estructura de la Base de Datos

### Tablas Principales (todas en español)

#### **usuarios** (users)
- Usuarios del sistema con roles y permisos

#### **votantes** (voters)
```
- id, ci (cédula), nombres, apellidos
- telefono, email, direccion
- barrio, zona, distrito
- latitud, longitud (geolocalización)
- fecha_nacimiento, genero, ocupacion
- lider_asignado_id (FK)
- ya_voto (bool), voto_registrado_en (datetime)
- necesita_transporte (bool)
- codigo_intencion (A/B/C/D/E)
- estado_contacto (Nuevo/Contactado/Re-contacto/Comprometido/Crítico)
- notas (text)
```

#### **contactos_votantes** (voter_contacts)
- Historial de todos los contactos con votantes
- Método (puerta/whatsapp/llamada), resultado, notas

#### **lideres** (leaders)
- Líderes con territorio asignado y meta de votos

#### **vehiculos** (vehicles)
- Placa, capacidad, consumo por km, costo por km

#### **choferes** (drivers)
- Datos del chofer, licencia, costo por viaje

#### **viajes** (trips)
- Planificación de transporte para día D
- Relación con vehículo, chofer y votantes

#### **pasajeros_viaje** (trip_passengers)
- Votantes asignados a cada viaje

#### **gastos** (expenses)
- Registro de todos los gastos de campaña
- Categoría, monto, fecha, recibo

#### **auditorias** (audits)
- Log de cambios críticos para trazabilidad

### Diagrama ERD

```
usuarios (1) -----> (N) votantes (lider_asignado_id)
votantes (1) -----> (N) contactos_votantes
usuarios (1) -----> (N) lideres
vehiculos (1) -----> (N) viajes
choferes (1) -----> (N) viajes
viajes (1) -----> (N) pasajeros_viaje -----> (1) votantes
usuarios (1) -----> (N) gastos
```

---

## 🎯 Funcionalidades Principales

### 1. Dashboard por Líder

**Componente Livewire**: `LeaderDashboard`

**Métricas mostradas**:
- Total votantes asignados
- Contactados vs. No contactados
- Distribución por intención (A/B/C/D/E)
- Necesitan transporte
- Ya votaron (tiempo real día D)

**Acciones rápidas**:
- Registrar nuevo contacto
- Marcar como votado
- Reasignar votante a otro líder
- Exportar lista a Excel/PDF
- Generar lista de transporte

### 2. Gestión Territorial

**Mapa interactivo** (Leaflet/OpenStreetMap):
- Heatmap por densidad de votantes
- Capas por intención de voto (colores)
- Búsqueda por dirección/barrio/manzana
- Agrupamiento de marcadores

### 3. Logística de Transporte

**Componente**: `TripPlanner`

**Funcionalidades**:
- Seleccionar votantes que necesitan transporte
- Asignar vehículo y chofer
- Algoritmo de agrupamiento por proximidad
- Cálculo automático de:
  - Viajes necesarios
  - Distancia estimada
  - Costo por combustible
  - Costo total (chofer + viáticos)

**Algoritmo de optimización**:
```
viajes_necesarios = ceil(n_votantes_transporte / capacidad_vehiculo)
costo_viaje = (distancia_km × consumo_km × precio_combustible) + costo_chofer + viaticos
```

### 4. Finanzas

**Módulo de gastos**:
- Registro de todos los gastos (categorías)
- Dashboard financiero con gráficos
- Reportes por período
- Cálculo de **Costo por Voto**

**Fórmula**:
```
Costo por Voto = Total Gastado / Votos Estimados
```

### 5. Predicción de Votos

**Service**: `PredictionService`

#### **Método Heurístico**
```php
votos_estimados = SUM(probabilidad_i)
```

Probabilidades por intención:
- A (Voto seguro): 1.0
- B (Probable): 0.7
- C (Indeciso): 0.5
- D (Difícil): 0.2
- E (Contrario): 0.0

#### **Método Monte Carlo**
Simula N iteraciones (ej. 1000) aplicando Bernoulli a cada votante.

**Retorna**:
- Media, mediana
- Percentiles 10 y 90
- Mínimo y máximo
- Histograma de distribución

### 6. Importación Masiva

**Componente**: `VoterImporter`

**Características**:
- Soporta CSV y XLSX
- Validación de campos
- Detección de duplicados (por CI/teléfono)
- Reporte detallado:
  - Registros nuevos
  - Duplicados
  - Actualizados
  - Errores con detalle

**Formato CSV esperado**:
```csv
ci,nombres,apellidos,telefono,email,direccion,barrio,codigo_intencion
1234567,Juan,Pérez,0981123456,juan@email.com,Av. Principal 123,Centro,A
```

### 7. Auditoría y Seguridad

**Tabla audits** registra:
- Usuario que realizó la acción
- Acción ejecutada
- Valores antiguos y nuevos (JSON)
- Timestamp

**Roles implementados**:
- **Super Admin**: acceso total
- **Coordinador**: gestiona zonas y líderes
- **Líder**: gestiona sus votantes y voluntarios
- **Voluntario**: registra contactos
- **Auditor**: solo lectura

---

## 🔌 API Endpoints

### Autenticación
```
POST /api/v1/login
POST /api/v1/logout
```

### Votantes
```
GET    /api/v1/votantes
POST   /api/v1/votantes
GET    /api/v1/votantes/{id}
PUT    /api/v1/votantes/{id}
DELETE /api/v1/votantes/{id}
PUT    /api/v1/votantes/{id}/marcar-voto
POST   /api/v1/votantes/importar
```

### Predicciones
```
GET /api/v1/predicciones?modelo=heuristico
GET /api/v1/predicciones?modelo=montecarlo&iteraciones=1000
```

**Respuesta ejemplo**:
```json
{
  "modelo": "montecarlo",
  "iteraciones": 1000,
  "estadisticas": {
    "media": 156.4,
    "mediana": 156,
    "min": 142,
    "max": 171,
    "p10": 148,
    "p90": 165
  },
  "histograma": [...]
}
```

### Viajes
```
GET  /api/v1/viajes
POST /api/v1/viajes
GET  /api/v1/viajes/{id}
PUT  /api/v1/viajes/{id}
```

### Gastos
```
GET  /api/v1/gastos
POST /api/v1/gastos
```

---

## 🧪 Testing

### Ejecutar Tests

```powershell
# Todos los tests
php artisan test

# Tests específicos
php artisan test --filter=VotanteTest

# Con cobertura
php artisan test --coverage
```

### Tests Implementados

1. ✅ **ImportacionVotantesTest**: Importar 1000 votantes desde CSV
2. ✅ **AsignacionLiderTest**: Asignar votantes a líder y verificar métricas
3. ✅ **PlanificacionViajeTest**: Planificar viaje con 12 votantes, vehículo capacidad 6
4. ✅ **CalculoCostoViajeTest**: Validar fórmula de costo por viaje
5. ✅ **PrediccionTest**: Ejecutar Monte Carlo con 1000 iteraciones

---

## 📊 Métricas Clave

### Fórmulas Implementadas

1. **Conversión de Contactos**
   ```
   (contactados / registrados) × 100
   ```

2. **Conversión a Votos Probables**
   ```
   ((#A + #B) / total_votantes) × 100
   ```

3. **Proyección de Votos (Heurístico)**
   ```
   SUM(probabilidad_i) sobre todos los votantes
   ```

4. **Costo por Voto**
   ```
   total_gastado / votos_estimados
   ```

5. **Viajes Necesarios**
   ```
   ceil(n_votantes_transporte / capacidad_vehiculo)
   ```

6. **ROI Estimado**
   ```
   ((votos_estimados × valor_voto) - total_gastado) / total_gastado
   ```

---

## ⚖️ Consideraciones Legales y Éticas

### ⚠️ IMPORTANTE - Privacidad y Protección de Datos

Este sistema maneja **datos personales sensibles** de ciudadanos. Es obligatorio:

1. **Consentimiento Informado**
   - Obtener consentimiento explícito de cada votante al registrar sus datos
   - Explicar claramente el uso que se dará a su información
   - Permitir revocación del consentimiento en cualquier momento

2. **Cumplimiento Legal**
   - Verificar legislación local de Paraguay sobre datos electorales
   - Consultar con asesor legal antes de usar en producción
   - Cumplir con normativas de protección de datos personales

3. **Retención y Eliminación**
   - Definir política de retención de datos (ej. eliminar 30 días post-elección)
   - Implementar proceso de anonimización o eliminación segura
   - No compartir datos con terceros sin consentimiento

4. **Seguridad**
   - Usar HTTPS en producción
   - Encriptar datos sensibles (CI, teléfonos)
   - Implementar autenticación de dos factores (2FA)
   - Auditar accesos a datos sensibles

5. **Transparencia**
   - Documentar todos los procesos de manejo de datos
   - Mantener registro de accesos y modificaciones (tabla audits)
   - Permitir a ciudadanos solicitar copia o eliminación de sus datos

### Aviso de Responsabilidad

Este software se proporciona "tal cual" sin garantías. Los desarrolladores y usuarios son responsables de:
- Cumplir con todas las leyes aplicables
- Obtener asesoría legal apropiada
- Implementar medidas de seguridad adicionales según sea necesario
- Verificar legalidad del uso de datos electorales en su jurisdicción

---

## 🔒 Seguridad en Producción

### Checklist antes de desplegar:

- [ ] `APP_DEBUG=false` en `.env`
- [ ] Cambiar `APP_KEY`
- [ ] Usar contraseñas seguras para DB
- [ ] Habilitar HTTPS (certificado SSL)
- [ ] Configurar firewall y restringir acceso a MySQL
- [ ] Implementar rate limiting en API
- [ ] Habilitar logs y monitoreo
- [ ] Backup automático de base de datos
- [ ] Implementar 2FA para administradores
- [ ] Encriptar datos sensibles en DB

---

## 🚧 Mejoras Futuras (Opcionales)

- [ ] Integración con padrón electoral (API si disponible)
- [ ] Notificaciones SMS/WhatsApp (Twilio, Meta Business)
- [ ] Optimización de rutas con Google Maps API o MapBox
- [ ] Autenticación con redes sociales (OAuth)
- [ ] App móvil (React Native / Flutter)
- [ ] Reconocimiento facial para verificación de voto
- [ ] Inteligencia artificial para predicción avanzada
- [ ] Sistema de encuestas integrado
- [ ] Panel de análisis de sentimiento (social media)

---

## 📞 Soporte y Contacto

Para reportar bugs o solicitar funcionalidades:
- Email: soporte@campana.com
- GitHub Issues: [Repositorio del proyecto]

---

## 📄 Licencia

Este proyecto es de código privado para uso exclusivo de campañas electorales autorizadas.

---

## 👥 Créditos

Desarrollado con Laravel 10, Livewire 3, Tailwind CSS 3 y MySQL.

**Stack tecnológico**:
- Backend: Laravel 10.x
- Frontend: Livewire 3.x + Alpine.js
- CSS: Tailwind CSS 3.x
- Database: MySQL 8.x
- Maps: Leaflet + OpenStreetMap
- Charts: Chart.js
- Icons: Heroicons

---

**¡Éxito en tu campaña! 🎉**
