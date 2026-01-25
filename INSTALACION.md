# 🚀 INSTRUCCIONES DE INSTALACIÓN - Sistema de Campaña

## Guía paso a paso para instalar y configurar el sistema en Laragon

---

## 📋 Prerrequisitos

Antes de comenzar, asegúrate de tener instalado:

- **Windows 10/11**
- **Laragon Full** (descarga desde https://laragon.org/download/)
- **Git** (opcional, para clonar repositorio)

---

## 🔧 Paso 1: Instalar y Configurar Laragon

### 1.1 Descargar e Instalar Laragon

1. Descarga **Laragon Full** desde https://laragon.org/download/
2. Ejecuta el instalador y sigue las instrucciones
3. Instala en la ruta predeterminada: `C:\laragon`
4. Marca las opciones:
   - ✅ Apache
   - ✅ PHP 8.1 o superior
   - ✅ MySQL
   - ✅ phpMyAdmin

### 1.2 Iniciar Laragon

1. Abre Laragon
2. Haz clic en **"Start All"** (botón azul)
3. Espera a que Apache y MySQL estén en verde
4. Verifica que funciona visitando: http://localhost

---

## 🗂️ Paso 2: Preparar el Proyecto

### 2.1 Copiar Archivos del Proyecto

**Opción A: Si tienes el proyecto en una carpeta**

1. Copia toda la carpeta `sisvoto` a `C:\laragon\www\`
2. La ruta final debe ser: `C:\laragon\www\sisvoto`

**Opción B: Si usas Git**

```powershell
# Abrir terminal de Laragon (clic derecho en icono > Terminal)
cd C:\laragon\www
git clone <URL_DEL_REPOSITORIO> sisvoto
cd sisvoto
```

### 2.2 Verificar Estructura

Asegúrate de que existan estas carpetas en `C:\laragon\www\sisvoto`:
- ✅ app/
- ✅ database/
- ✅ public/
- ✅ resources/
- ✅ routes/
- ✅ composer.json
- ✅ .env.example

---

## 🗄️ Paso 3: Crear Base de Datos

### 3.1 Crear DB desde Terminal

1. Abre **Terminal de Laragon** (clic derecho en icono Laragon > Terminal)
2. Ejecuta:

```powershell
mysql -u root -e "CREATE DATABASE IF NOT EXISTS campana CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 3.2 Verificar Creación (Opcional)

```powershell
mysql -u root -e "SHOW DATABASES;"
```

Debes ver `campana` en la lista.

---

## ⚙️ Paso 4: Configurar el Proyecto

### 4.1 Copiar Archivo de Configuración

```powershell
cd C:\laragon\www\sisvoto
copy .env.example .env
```

### 4.2 Editar Archivo .env

Abre `.env` con un editor de texto (Notepad++, VSCode, etc.) y verifica:

```env
APP_NAME="Sistema Campaña"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://campana.local

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=campana
DB_USERNAME=root
DB_PASSWORD=

PRECIO_COMBUSTIBLE=7500
```

**⚠️ IMPORTANTE:** No cambies `DB_PASSWORD`, debe estar vacío para Laragon.

---

## 📦 Paso 5: Instalar Dependencias

### 5.1 Instalar Dependencias PHP (Composer)

```powershell
cd C:\laragon\www\sisvoto
composer install
```

⏳ Esto puede tardar 2-5 minutos.

### 5.2 Generar Clave de Aplicación

```powershell
php artisan key:generate
```

### 5.3 Instalar Dependencias Node.js

```powershell
npm install
```

⏳ Esto puede tardar 3-7 minutos dependiendo de tu conexión.

---

## 🗃️ Paso 6: Migrar y Poblar Base de Datos

### 6.1 Ejecutar Migraciones

```powershell
php artisan migrate
```

Debes ver mensajes como:
```
✓ 2024_01_01_000001_create_roles_table
✓ 2024_01_01_000002_add_role_fields_to_users_table
...
```

### 6.2 Poblar con Datos de Ejemplo

```powershell
php artisan db:seed
```

Verás un resumen:
```
✅ Base de datos poblada exitosamente!
📊 Resumen:
   - Roles: 5
   - Usuarios: ~17
   - Líderes: 5
   - Votantes: 250
   ...
```

**💡 TIP:** Si algo sale mal, puedes resetear todo con:
```powershell
php artisan migrate:fresh --seed
```

---

## 🌐 Paso 7: Configurar Virtual Host

### 7.1 Crear Virtual Host en Laragon

1. **Clic derecho en icono de Laragon**
2. Selecciona: **Apache > sites-enabled > Add**
3. En el formulario:
   - **Name:** `campana.local`
   - **Path:** `C:\laragon\www\sisvoto\public`
4. Clic en **OK**

### 7.2 Reiniciar Laragon

1. Clic en **"Stop All"**
2. Clic en **"Start All"**
3. Espera a que Apache y MySQL estén en verde

### 7.3 Verificar Hosts

Laragon debería agregar automáticamente la entrada al archivo hosts.
Para verificar, abre PowerShell como Administrador:

```powershell
notepad C:\Windows\System32\drivers\etc\hosts
```

Debe contener una línea como:
```
127.0.0.1    campana.local
```

Si no está, agrégala manualmente.

---

## 🎨 Paso 8: Compilar Assets Frontend

### 8.1 Compilar para Desarrollo

**Opción A: Compilar una vez**
```powershell
npm run dev
```

**Opción B: Modo watch (recompila automáticamente)**
```powershell
npm run watch
```

**Opción C: Para producción**
```powershell
npm run build
```

---

## ✅ Paso 9: Verificar Instalación

### 9.1 Abrir Navegador

Visita: **http://campana.local**

Debes ver la página de inicio del sistema.

### 9.2 Probar API

Abre navegador y visita:

**http://campana.local/api/v1/health**

Debes ver:
```json
{
  "status": "ok",
  "timestamp": "2024-01-15T10:30:00Z",
  "version": "1.0.0"
}
```

### 9.3 Probar Métricas Generales

**http://campana.local/api/v1/metricas/generales**

Debes ver estadísticas de votantes.

### 9.4 Probar Predicción Heurística

**http://campana.local/api/v1/predicciones/heuristico**

Debes ver estimación de votos.

---

## 🔑 Credenciales de Acceso

Los usuarios creados por el seeder son:

| Email | Password | Rol |
|-------|----------|-----|
| admin@campana.com | password | Super Admin |
| coordinador@campana.com | password | Coordinador |
| lider@campana.com | password | Líder |
| voluntario@campana.com | password | Voluntario |
| auditor@campana.com | password | Auditor |

---

## 🧪 Paso 10: Ejecutar Tests (Opcional)

```powershell
php artisan test
```

Debes ver todos los tests en verde (PASSED).

---

## 🔧 Solución de Problemas Comunes

### ❌ Error: "Access denied for user 'root'"

**Solución:**
- Verifica que MySQL esté corriendo en Laragon
- Verifica que `DB_PASSWORD` en `.env` esté vacío

### ❌ Error: "Class 'Dotenv' not found"

**Solución:**
```powershell
composer install
php artisan key:generate
```

### ❌ Error: "No application encryption key"

**Solución:**
```powershell
php artisan key:generate
```

### ❌ La página muestra estilos rotos

**Solución:**
```powershell
npm install
npm run dev
php artisan cache:clear
```

### ❌ Error 404 en API

**Solución:**
```powershell
php artisan route:clear
php artisan config:clear
```

### ❌ Virtual host no funciona

**Solución:**
1. Verifica `C:\Windows\System32\drivers\etc\hosts`
2. Debe contener: `127.0.0.1    campana.local`
3. Reinicia Laragon
4. Limpia caché del navegador (Ctrl + Shift + Delete)

---

## 📡 Endpoints API Disponibles

### 🔍 Votantes

```
GET    /api/v1/votantes
POST   /api/v1/votantes
GET    /api/v1/votantes/{id}
PUT    /api/v1/votantes/{id}
DELETE /api/v1/votantes/{id}
PUT    /api/v1/votantes/{id}/marcar-voto
POST   /api/v1/votantes/reasignar-lider
```

### 📊 Predicciones

```
GET /api/v1/predicciones/heuristico
GET /api/v1/predicciones/montecarlo?iteraciones=1000
GET /api/v1/predicciones/combinado?iteraciones=1000
```

### 📈 Métricas

```
GET /api/v1/metricas/generales
GET /api/v1/metricas/lider/{id}
GET /api/v1/metricas/conversion-contactos
GET /api/v1/metricas/costo-por-voto
GET /api/v1/metricas/roi?valor_por_voto=50000
```

### 🚗 Viajes

```
GET  /api/v1/viajes
POST /api/v1/viajes
GET  /api/v1/viajes/{id}
PUT  /api/v1/viajes/{id}
POST /api/v1/viajes/generar-plan
```

### 💰 Gastos

```
GET /api/v1/gastos
POST /api/v1/gastos
GET /api/v1/gastos/{id}
PUT /api/v1/gastos/{id}/aprobar
GET /api/v1/gastos/resumen/por-categoria
```

### 📥 Importación

```
POST /api/v1/importacion/votantes
GET  /api/v1/importacion/plantilla
```

---

## 🧰 Comandos Útiles

### Limpiar Caché

```powershell
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Ver Rutas Disponibles

```powershell
php artisan route:list
```

### Abrir Tinker (Consola Interactiva)

```powershell
php artisan tinker
```

Ejemplos en Tinker:
```php
// Contar votantes
App\Models\Votante::count()

// Ver todos los roles
App\Models\Role::all()

// Crear un usuario
$user = new App\Models\User();
$user->name = "Nuevo Usuario";
$user->email = "nuevo@test.com";
$user->password = bcrypt('password');
$user->save();
```

### Resetear Base de Datos

```powershell
php artisan migrate:fresh --seed
```

⚠️ **CUIDADO:** Esto elimina todos los datos existentes.

---

## 📝 Próximos Pasos

1. **Explorar la API**: Usa Postman o Insomnia para probar los endpoints
2. **Revisar el código**: Familiarízate con Models, Services y Controllers
3. **Personalizar**: Modifica según necesidades específicas
4. **Integrar Frontend**: Considera usar Livewire o Vue.js
5. **Desplegar**: Cuando esté listo, despliega en servidor de producción

---

## 🆘 Soporte

Si encuentras problemas:

1. Revisa los logs: `storage/logs/laravel.log`
2. Ejecuta `php artisan tinker` para debuggear
3. Consulta la documentación oficial de Laravel: https://laravel.com/docs

---

## ✅ Checklist Final

- [ ] Laragon instalado y funcionando
- [ ] Base de datos `campana` creada
- [ ] Proyecto en `C:\laragon\www\sisvoto`
- [ ] `composer install` ejecutado
- [ ] `npm install` ejecutado
- [ ] `.env` configurado correctamente
- [ ] `php artisan key:generate` ejecutado
- [ ] `php artisan migrate --seed` ejecutado
- [ ] Virtual host `campana.local` configurado
- [ ] http://campana.local funciona
- [ ] API responde correctamente
- [ ] Tests pasan exitosamente

---

**¡Felicitaciones! El sistema está instalado y listo para usar. 🎉**
