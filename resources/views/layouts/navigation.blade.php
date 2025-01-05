<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 sticky top-0 z-50 shadow-md">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
                        SocialApp
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" 
                        class="text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                        <svg class="w-5 h-5 mr-1 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-2-2h-2"></path>
                        </svg>
                        {{ __('News Feed') }}
                    </x-nav-link>

                    <!-- Add Messages Link with Counter -->
                    <x-nav-link :href="route('messages')" :active="request()->routeIs('messages')" 
                        class="text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors relative">
                        <svg class="w-5 h-5 mr-1 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                        {{ __('Messages') }}
                        <span 
                            ng-if="unreadMessageCount > 0"
                            class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full"
                        >
                            [[ unreadMessageCount ]]
                        </span>
                    </x-nav-link>
                </div>
            </div>

            <!-- Notification Dropdown (keep your existing notification code, just update the button styling) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 mr-4" ng-controller="NotificationController">
                <div class="relative">
                    <button 
                        ng-click="toggleNotifications($event)" 
                        class="relative p-2 text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors focus:outline-none"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <!-- Notification Badge -->
                        <span 
                            ng-if="unreadCount > 0"
                            class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full"
                        >
                            [[ unreadCount ]]
                        </span>
                    </button>

                    <!-- Notification Dropdown -->
                    <div 
                        ng-show="showNotifications"
                        class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-md shadow-lg overflow-hidden z-50"
                        ng-click="$event.stopPropagation()"
                    >
                        <div class="py-2">
                            <div class="flex items-center justify-between px-4 py-2 border-b dark:border-gray-700">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-200">Notifications</h3>
                                <button 
                                    ng-if="unreadCount > 0"
                                    ng-click="markAllAsRead()"
                                    class="text-xs text-blue-500 hover:text-blue-600"
                                >
                                    Mark all as read
                                </button>
                            </div>

                            <!-- Notification List -->
                            <div class="max-h-64 overflow-y-auto">
                                <div 
                                    ng-repeat="notification in notifications"
                                    ng-click="markAsRead(notification)"
                                    class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
                                    ng-class="{'bg-blue-50 dark:bg-gray-700': !notification.is_read, '[[ notification.typeClass ]]': true}"
                                >
                                    <div class="flex items-center">
                                        <svg ng-if="notification.type === 'post'" class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        <svg ng-if="notification.type === 'like'" class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                        </svg>
                                        <svg ng-if="notification.type === 'comment'" class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                        </svg>
                                        <p class="text-sm text-gray-900 dark:text-gray-200">[[ notification.message ]]</p>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">[[ notification.created_at | date:'medium' ]]</p>
                                </div>
                                
                                <div ng-if="notifications.length === 0" class="px-4 py-3 text-sm text-gray-500 text-center">
                                    No notifications
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-gray-200 dark:border-gray-600 text-sm leading-4 font-medium rounded-full text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none transition ease-in-out duration-150 shadow-sm">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full overflow-hidden mr-2 ring-2 ring-indigo-500/20 dark:ring-indigo-400/20">
                                    <img src="{{ Auth::user()->profile_picture ? Storage::url(Auth::user()->profile_picture) : asset('storage/profile-pictures/default-avatar.jpg') }}"
                                        alt="{{ Auth::user()->name }}"
                                        class="w-full h-full object-cover"
                                    >
                                </div>
                                <div>{{ Auth::user()->name }}</div>
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700">
                            <x-dropdown-link :href="route('user.profile')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4 flex items-center">
                <!-- Mobile Profile Picture -->
                <div class="w-10 h-10 rounded-full overflow-hidden mr-3 ring-2 ring-gray-200 dark:ring-gray-600">
                    <img 
                        src="{{ Auth::user()->profile_picture ? Storage::url(Auth::user()->profile_picture) : asset('storage/profile-pictures/default-avatar.jpg') }}"
                        alt="{{ Auth::user()->name }}"
                        class="w-full h-full object-cover"
                    >
                </div>
                <div>
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('user.profile')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
