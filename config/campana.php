<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuración de Campaña
    |--------------------------------------------------------------------------
    |
    | Valores de configuración específicos para el sistema de gestión
    | de campaña política.
    |
    */

    // Precio del combustible por litro (en guaraníes)
    'precio_combustible' => env('PRECIO_COMBUSTIBLE', 7500),

    // Clave API de MapBox (opcional)
    'mapbox_key' => env('MAPBOX_KEY', ''),

    // Configuración de predicción
    'prediccion' => [
        'iteraciones_default' => env('PREDICCION_ITERACIONES_DEFAULT', 1000),
        'probabilidades' => [
            'A' => env('PROB_INTENCION_A', 1.0),  // Voto seguro
            'B' => env('PROB_INTENCION_B', 0.7),  // Probable
            'C' => env('PROB_INTENCION_C', 0.5),  // Indeciso
            'D' => env('PROB_INTENCION_D', 0.2),  // Difícil
            'E' => env('PROB_INTENCION_E', 0.0),  // Contrario
        ],
    ],

    // Configuración de importación
    'importacion' => [
        'max_file_size' => env('MAX_IMPORT_FILE_SIZE', 10240), // KB
        'chunk_size' => env('IMPORT_CHUNK_SIZE', 500),
        'formatos_permitidos' => ['csv', 'xlsx', 'xls'],
    ],

    // Configuración de auditoría
    'auditoria' => [
        'enabled' => env('AUDIT_ENABLED', true),
        'retention_days' => env('AUDIT_RETENTION_DAYS', 365),
    ],

    // Rol por defecto al registrar usuarios
    'default_role' => env('DEFAULT_ROLE', 'voluntario'),

    // Códigos de intención de voto
    'codigos_intencion' => [
        'A' => 'Voto seguro',
        'B' => 'Probable',
        'C' => 'Indeciso',
        'D' => 'Difícil',
        'E' => 'Contrario',
    ],

    // Estados de contacto
    'estados_contacto' => [
        'Nuevo',
        'Contactado',
        'Re-contacto',
        'Comprometido',
        'Crítico',
    ],

    // Métodos de contacto
    'metodos_contacto' => [
        'Puerta a puerta',
        'WhatsApp',
        'Llamada',
        'Visita programada',
        'Evento',
        'Otro',
    ],

    // Categorías de gastos
    'categorias_gastos' => [
        'Combustible',
        'Transporte',
        'Publicidad',
        'Material impreso',
        'Eventos',
        'Alimentos',
        'Tecnología',
        'Personal',
        'Otros',
    ],

    // Estados de viajes
    'estados_viajes' => [
        'Planificado',
        'Confirmado',
        'En curso',
        'Completado',
        'Cancelado',
    ],

];
