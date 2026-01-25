<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Logo/Header -->
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900">Sistema Campaña</h2>
                    <p class="mt-2 text-sm text-gray-600">Ingrese sus credenciales para acceder</p>
                </div>

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email
                        </label>
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="usuario@ejemplo.com">
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            Contraseña
                        </label>
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="••••••••">
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                name="remember" 
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-600">Recordarme</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        Iniciar Sesión
                    </button>
                </form>

                <!-- Demo Credentials Info -->
                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                    <p class="text-xs font-medium text-blue-900 mb-2">Credenciales de prueba:</p>
                    <div class="text-xs text-blue-800 space-y-1">
                        <p><strong>Admin:</strong> admin@campana.com</p>
                        <p><strong>Coordinador:</strong> coordinador@campana.com</p>
                        <p><strong>Líder:</strong> lider@campana.com</p>
                        <p class="mt-2"><em>Contraseña para todos: password</em></p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <p class="mt-4 text-center text-xs text-gray-500">
                &copy; {{ date('Y') }} Sistema de Campaña. Todos los derechos reservados.
            </p>
        </div>
    </div>
</body>
</html>
