<x-app-layout>
    @push('scripts')
    <script>
        window.initialUsers = {!! json_encode($users) !!};
    </script>
    @endpush

    <div class="py-12" ng-controller="MessageController">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 flex h-[600px]">
                    <!-- Users List -->
                    <div class="w-1/4 border-r dark:border-gray-700">
                        <div class="mb-4">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Messages</h2>
                            <!-- Add Search Input -->
                            <div class="mt-2">
                                <input 
                                    type="text" 
                                    ng-model="searchQuery"
                                    ng-change="filterUsers()"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm"
                                    placeholder="Search users..."
                                >
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div 
                                ng-repeat="user in filteredUsers"
                                ng-click="selectUser(user)"
                                class="p-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors"
                                ng-class="{'bg-gray-50 dark:bg-gray-700': selectedUser.id === user.id}"
                            >
                                <div class="flex items-center">
                                    <img 
                                        ng-src="[[ user.profile_picture ]]" 
                                        class="w-10 h-10 rounded-full object-cover"
                                        alt="[[ user.name ]]"
                                    >
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">[[ user.name ]]</p>
                                    </div>
                                </div>
                            </div>
                            <!-- No Results Message -->
                            <div ng-if="filteredUsers.length === 0" class="p-3 text-center text-gray-500 dark:text-gray-400">
                                No users found
                            </div>
                        </div>
                    </div>

                    <!-- Messages Area -->
                    <div class="w-3/4 flex flex-col">
                        <div ng-if="!selectedUser" class="h-full flex items-center justify-center">
                            <p class="text-gray-500 dark:text-gray-400">Select a user to start messaging</p>
                        </div>

                        <div ng-if="selectedUser" class="h-full flex flex-col">
                            <!-- Chat Header -->
                            <div class="p-4 border-b dark:border-gray-700">
                                <div class="flex items-center">
                                    <img 
                                        ng-src="[[ selectedUser.profile_picture ]]" 
                                        class="w-10 h-10 rounded-full object-cover"
                                        alt="[[ selectedUser.name ]]"
                                    >
                                    <h3 class="ml-3 text-lg font-medium text-gray-900 dark:text-gray-100">
                                        [[ selectedUser.name ]]
                                    </h3>
                                </div>
                            </div>

                            <!-- Messages -->
                            <div class="flex-1 overflow-y-auto p-4 space-y-4" id="messages-container">
                                <div 
                                    ng-repeat="message in messages"
                                    class="flex"
                                    ng-class="{'justify-end': message.sender_id === currentUser.id}"
                                >
                                    <div 
                                        class="max-w-[70%] rounded-lg px-4 py-2"
                                        ng-class="{
                                            'bg-indigo-500 text-white': message.sender_id === currentUser.id,
                                            'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100': message.sender_id !== currentUser.id
                                        }"
                                    >
                                        <p>[[ message.content ]]</p>
                                        <p class="text-xs mt-1 opacity-70">[[ message.created_at | date:'short' ]]</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Message Input -->
                            <div class="p-4 border-t dark:border-gray-700">
                                <div class="flex space-x-2">
                                    <input 
                                        type="text" 
                                        ng-model="newMessage"
                                        ng-model-options="{ updateOn: 'default blur', debounce: { default: 0, blur: 0 } }"
                                        ng-keypress="$event.keyCode === 13 && sendMessage()"
                                        class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Type your message..."
                                    >
                                    <button 
                                        ng-click="sendMessage()"
                                        ng-disabled="!newMessage || newMessage.trim().length === 0"
                                        class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        Send
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 