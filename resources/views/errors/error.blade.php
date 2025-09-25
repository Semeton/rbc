<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Error {{ $exception->getStatusCode() ?? '500' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="text-center">
                <!-- Error Icon -->
                <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-red-100 dark:bg-red-900/20 mb-6">
                    @if(($exception->getStatusCode() ?? 500) === 403)
                        <svg class="h-12 w-12 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    @elseif(($exception->getStatusCode() ?? 500) === 404)
                        <svg class="h-12 w-12 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @else
                        <svg class="h-12 w-12 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                    @endif
                </div>

                <!-- Error Code -->
                <h1 class="text-6xl font-bold text-zinc-900 dark:text-zinc-100 mb-4">
                    {{ $exception->getStatusCode() ?? '500' }}
                </h1>
                
                <!-- Error Title -->
                <h2 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                    @if(($exception->getStatusCode() ?? 500) === 403)
                        Access Denied
                    @elseif(($exception->getStatusCode() ?? 500) === 404)
                        Page Not Found
                    @elseif(($exception->getStatusCode() ?? 500) === 500)
                        Server Error
                    @else
                        Error
                    @endif
                </h2>
                
                <!-- Error Message -->
                <p class="text-lg text-zinc-600 dark:text-zinc-400 mb-8">
                    @if(($exception->getStatusCode() ?? 500) === 403)
                        You don't have permission to access this resource.
                    @elseif(($exception->getStatusCode() ?? 500) === 404)
                        The page you're looking for doesn't exist.
                    @elseif(($exception->getStatusCode() ?? 500) === 500)
                        Something went wrong on our end.
                    @else
                        An error occurred while processing your request.
                    @endif
                </p>

                <!-- User Role Info (if authenticated and 403) -->
                @auth
                    @if(($exception->getStatusCode() ?? 500) === 403)
                        <div class="bg-zinc-100 dark:bg-zinc-800 rounded-lg p-4 mb-8">
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-2">
                                <strong>Current Role:</strong> {{ Auth::user()->role() }}
                            </p>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                <strong>User:</strong> {{ Auth::user()->name }} ({{ Auth::user()->email }})
                            </p>
                        </div>
                    @endif
                @endauth

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('dashboard.index') }}" 
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Go to Dashboard
                    </a>
                    
                    <button onclick="history.back()" 
                            class="inline-flex items-center px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-base font-medium rounded-md text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Go Back
                    </button>
                </div>

                <!-- Additional Help -->
                <div class="mt-8 text-center">
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        @if(($exception->getStatusCode() ?? 500) === 403)
                            If you believe this is an error, please contact your administrator.
                        @elseif(($exception->getStatusCode() ?? 500) === 404)
                            Check the URL or go back to the previous page.
                        @else
                            Please try again later or contact support if the problem persists.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
