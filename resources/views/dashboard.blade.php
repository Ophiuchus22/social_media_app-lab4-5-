<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('News Feed') }}
        </h2>
    </x-slot>

    <div class="py-12" ng-app="socialApp" ng-controller="PostController">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <!-- Post Creation Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form ng-submit="createPost()">
                        <textarea 
                            ng-model="newPost.content"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-blue-500 focus:border-blue-500"
                            rows="3"
                            placeholder="What's on your mind?"
                        ></textarea>
                        <div class="mt-2 flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                                Post
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Posts List -->
            <div class="space-y-4">
                <div ng-repeat="post in posts" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-5">
                        <!-- Post Header -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full overflow-hidden ring-2 ring-gray-200 dark:ring-gray-700">
                                    <img 
                                        ng-src="/storage/[[ post.user.profile_picture ]]" 
                                        ng-if="post.user.profile_picture"
                                        class="w-full h-full object-cover"
                                        onerror="this.src='/storage/profile-pictures/default-avatar.jpg'"
                                    >
                                    <img 
                                        ng-src="/storage/profile-pictures/default-avatar.jpg" 
                                        ng-if="!post.user.profile_picture"
                                        class="w-full h-full object-cover"
                                    >
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">[[ post.user.name ]]</h3>
                                    <p class="text-xs text-gray-500">[[ post.created_at | date:'medium' ]]</p>
                                </div>
                            </div>
                            <div class="flex space-x-2" ng-if="post.user.id == currentUser.id">
                                <button 
                                    ng-click="editPost(post)" 
                                    class="text-gray-400 hover:text-blue-500 transition-colors"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button 
                                    ng-click="deletePost(post)" 
                                    class="text-gray-400 hover:text-red-500 transition-colors"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Post Content -->
                        <div ng-if="!post.editing">
                            <p class="text-gray-900 dark:text-gray-100 mb-4 text-sm leading-relaxed">[[ post.content ]]</p>
                        </div>
                        <div ng-if="post.editing" class="mb-4">
                            <textarea 
                                ng-model="post.editedContent" 
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                rows="3"
                            ></textarea>
                            <div class="flex justify-end space-x-2 mt-2">
                                <button 
                                    ng-click="post.editing = false" 
                                    class="px-3 py-1 text-sm text-gray-200 hover:text-gray-400 transition-colors"
                                >
                                    Cancel
                                </button>
                                <button 
                                    ng-click="updatePost(post)" 
                                    class="px-3 py-1 bg-blue-500 text-white text-sm rounded-md hover:bg-blue-600 transition-colors"
                                >
                                    Save
                                </button>
                            </div>
                        </div>

                        <!-- Post Actions -->
                        <div class="flex items-center space-x-4 border-t border-b border-gray-100 dark:border-gray-700 py-2 mb-4">
                            <button 
                                ng-click="toggleLike(post)" 
                                class="flex items-center space-x-2 text-sm"
                                ng-class="{'text-blue-500': post.liked, 'text-gray-500': !post.liked}"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                </svg>
                                <span>[[ post.likes.length ]] Likes</span>
                            </button>
                            <button 
                                ng-click="post.showComments = !post.showComments" 
                                class="flex items-center space-x-2 text-sm text-gray-500"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <span>[[ post.comments.length ]] Comments</span>
                            </button>
                        </div>

                        <!-- Comments Section -->
                        <div ng-show="post.showComments" class="space-y-3">
                            <!-- Comment List -->
                            <div ng-repeat="comment in post.comments" class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-7 h-7 rounded-full overflow-hidden ring-1 ring-gray-200 dark:ring-gray-600">
                                            <img 
                                                ng-src="/storage/[[ comment.user.profile_picture ]]" 
                                                ng-if="comment.user.profile_picture"
                                                class="w-full h-full object-cover"
                                                onerror="this.src='/storage/profile-pictures/default-avatar.jpg'"
                                            >
                                            <img 
                                                ng-src="/storage/profile-pictures/default-avatar.jpg" 
                                                ng-if="!comment.user.profile_picture"
                                                class="w-full h-full object-cover"
                                            >
                                        </div>
                                        <span class="font-medium text-sm text-gray-900 dark:text-gray-100">[[ comment.user.name ]]</span>
                                    </div>
                                    <button 
                                        ng-if="comment.user.id == currentUser.id"
                                        ng-click="deleteComment(post, comment)" 
                                        class="text-gray-400 hover:text-red-500 transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                                <p class="text-sm text-gray-700 dark:text-gray-300 ml-9">[[ comment.content ]]</p>
                            </div>

                            <!-- Comment Form -->
                            <form ng-submit="addComment(post)" class="mt-3">
                                <div class="flex space-x-2">
                                    <input 
                                        type="text" 
                                        ng-model="post.newComment" 
                                        class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Write a comment..."
                                    >
                                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white text-sm rounded-md hover:bg-blue-600 transition-colors">
                                        Comment
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        window.Laravel = {!! json_encode([
            'user' => auth()->user()
        ]) !!};
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @endpush
</x-app-layout>