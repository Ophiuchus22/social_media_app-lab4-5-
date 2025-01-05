<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" ng-app="socialApp">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Add these before your other scripts -->
        <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.15.3/echo.iife.min.js"></script>
        <script>
            window.Laravel = {!! json_encode([
                'user' => auth()->user()
            ]) !!};

            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: '{{ config('broadcasting.connections.pusher.key') }}',
                cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
                encrypted: true,
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }
            });
        </script>

        <!-- Your existing scripts -->
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>
        <script src="{{ asset('js/app.js') }}"></script>
        @stack('scripts')

        <!-- Right before closing </body> tag -->
        <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
        <script>
            const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
                cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}'
            });

            console.log('Pusher initialized for user:', window.Laravel.user.id);

            const channel = pusher.subscribe('notifications.' + window.Laravel.user.id);
            
            channel.bind('NewNotification', function(data) {
                console.log('Notification received:', data);
                var notificationScope = angular.element(document.querySelector('[ng-controller="NotificationController"]')).scope();
                if (notificationScope) {
                    notificationScope.$apply(function() {
                        notificationScope.notifications.unshift(data.notification);
                        notificationScope.unreadCount++;
                    });
                }
            });

            // Debug logs
            pusher.connection.bind('connected', () => {
                console.log('Successfully connected to Pusher');
            });

            channel.bind('pusher:subscription_succeeded', () => {
                console.log('Successfully subscribed to channel:', channel.name);
            });
        </script>
    </body>
</html>
