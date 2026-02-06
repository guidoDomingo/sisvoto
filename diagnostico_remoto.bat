@echo off
echo === DIAGNÓSTICO SERVIDOR REMOTO ===
echo.
echo Ejecutando comando de diagnóstico...
php artisan diagnosticar:roles
echo.
echo Verificando usuario actualmente logueado en navegador...
echo Sube este archivo y ejecutalo en tu servidor remoto, luego compara los resultados