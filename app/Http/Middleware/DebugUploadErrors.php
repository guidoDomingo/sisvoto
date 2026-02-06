<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DebugUploadErrors
{
    public function handle(Request $request, Closure $next)
    {
        // Solo aplicar a rutas de Livewire upload
        if (!$request->is('livewire/upload-file') && !$request->is('livewire/upload-file/*')) {
            return $next($request);
        }

        Log::info('UPLOAD DEBUG - Request incoming', [
            'url' => $request->url(),
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'content_length' => $request->header('Content-Length'),
            'user_agent' => $request->header('User-Agent'),
            'files_count' => count($request->allFiles()),
            'has_file' => $request->hasFile('file'),
            'session_id' => $request->session()->getId(),
        ]);

        $response = $next($request);

        // Si la respuesta es 422, capturar detalles
        if ($response->status() === 422) {
            Log::error('UPLOAD DEBUG - 422 Error', [
                'response_content' => $response->getContent(),
                'request_size' => strlen($request->getContent()),
                'php_upload_max_filesize' => ini_get('upload_max_filesize'),
                'php_post_max_size' => ini_get('post_max_size'),
                'php_max_execution_time' => ini_get('max_execution_time'),
                'livewire_config' => config('livewire.temporary_file_upload'),
            ]);
        }

        return $response;
    }
}