<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('User Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 relative">
                    <!-- Edit Button -->
                    <a href="{{ route('profile.edit') }}" class="absolute top-4 right-4 inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        Edit Profile
                    </a>
                    
                    <div class="flex items-center space-x-4 mb-6">
                        <!-- Profile Picture -->
                        <div class="w-24 h-24 rounded-full overflow-hidden">
                            <img src="{{ Auth::user()->profile_picture ? Storage::url(Auth::user()->profile_picture) : asset('storage/profile-pictures/default-avatar.jpg') }}" alt="Profile Picture" width="150" height="150">
                        </div>
                        <div>
                            <!-- Name -->
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ Auth::user()->name }}
                            </h3>
                            <!-- Email -->
                            <p class="text-gray-600 dark:text-gray-400">
                                {{ Auth::user()->email }}
                            </p>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Address</h4>
                        <p class="text-gray-700 dark:text-gray-300">
                            {{ Auth::user()->address ?? 'No address provided.' }}
                        </p>
                    </div>
                    
                    <!-- Bio -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Bio</h4>
                        <p class="text-gray-700 dark:text-gray-300">
                            {{ Auth::user()->bio ?? 'No bio available. Add your bio to tell others about yourself!' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Posts -->
            <!-- <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Posts</h4>
                    @forelse (Auth::user()->posts ?? [] as $post)
                        <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg mb-4">
                            <p class="text-gray-800 dark:text-gray-200">{{ $post->content }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Posted on {{ $post->created_at->format('M d, Y') }}</p>
                        </div>
                    @empty
                        <p class="text-gray-700 dark:text-gray-300">No posts yet. Share your thoughts!</p>
                    @endforelse
                </div>
            </div> -->
        </div>
    </div>
</x-app-layout>