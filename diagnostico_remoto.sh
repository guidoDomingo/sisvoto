#!/bin/bash
echo "=== DIAGNÓSTICO SERVIDOR REMOTO ==="
echo ""
echo "Ejecutando comando de diagnóstico..."
php artisan diagnosticar:roles
echo ""
echo "Verificando configuración de sesión..."
php artisan tinker --execute="
echo 'Driver de sesión: ' . config('session.driver') . PHP_EOL;
echo 'Lifetime de sesión: ' . config('session.lifetime') . ' minutos' . PHP_EOL;
echo 'APP_ENV: ' . config('app.env') . PHP_EOL;
echo 'APP_DEBUG: ' . (config('app.debug') ? 'true' : 'false') . PHP_EOL;
"