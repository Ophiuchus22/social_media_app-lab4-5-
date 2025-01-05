// public/js/app.js
var app = angular.module('socialApp', []) || angular.module('socialApp');

app.config(['$interpolateProvider', function($interpolateProvider) {
    $interpolateProvider.startSymbol('[[');
    $interpolateProvider.endSymbol(']]');
}]);

app.config(['$httpProvider', function($httpProvider) {
    $httpProvider.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}]);

app.controller('PostController', ['$scope', '$http', function($scope, $http) {
    $scope.posts = [];
    $scope.newPost = { content: '' };
    $scope.currentUser = window.Laravel.user;

    // Add CSRF token to all requests
    $http.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Fetch all posts
    $scope.getPosts = function() {
        $http.get('/api/posts')
            .then(function(response) {
                console.log('Posts retrieved:', response.data);
                $scope.posts = response.data;
                $scope.posts.forEach(function(post) {
                    post.liked = post.likes.some(like => like.user_id === $scope.currentUser.id);
                });
            })
            .catch(function(error) {
                console.error('Error fetching posts:', error);
            });
    };

    // Create a new post
    $scope.createPost = function() {
        if (!$scope.newPost.content) return;
        
        console.log('Creating post:', $scope.newPost);
        
        $http.post('/api/posts', $scope.newPost)
            .then(function(response) {
                console.log('Post created:', response.data);
                $scope.posts.unshift(response.data);
                $scope.newPost.content = '';
            })
            .catch(function(error) {
                console.error('Error creating post:', error);
                alert('Error creating post. Please try again.');
            });
    };

    // Delete a post
    $scope.deletePost = function(post) {
        if (!confirm('Are you sure you want to delete this post?')) return;

        $http.delete('/api/posts/' + post.id)
            .then(function() {
                const index = $scope.posts.indexOf(post);
                $scope.posts.splice(index, 1);
            })
            .catch(function(error) {
                console.error('Error deleting post:', error);
                alert('Error deleting post. Please try again.');
            });
    };

    // Toggle like on a post
    $scope.toggleLike = function(post) {
        $http.post('/api/posts/' + post.id + '/like')
            .then(function(response) {
                if (response.data.liked) {
                    post.likes.push({ user_id: $scope.currentUser.id });
                } else {
                    post.likes = post.likes.filter(like => like.user_id !== $scope.currentUser.id);
                }
                post.liked = response.data.liked;
            })
            .catch(function(error) {
                console.error('Error toggling like:', error);
                alert('Error toggling like. Please try again.');
            });
    };

    // Add a comment to a post
    $scope.addComment = function(post) {
        if (!post.newComment) return;

        $http.post('/api/posts/' + post.id + '/comments', {
            content: post.newComment
        })
            .then(function(response) {
                post.comments.push(response.data);
                post.newComment = '';
            })
            .catch(function(error) {
                console.error('Error adding comment:', error);
                alert('Error adding comment. Please try again.');
            });
    };

    // Edit post
    $scope.editPost = function(post) {
        post.editing = true;
        post.editedContent = post.content;
    };

    // Save edited post
    $scope.updatePost = function(post) {
        $http.put('/api/posts/' + post.id, {
            content: post.editedContent
        })
        .then(function(response) {
            post.content = post.editedContent;
            post.editing = false;
        })
        .catch(function(error) {
            console.error('Error updating post:', error);
            alert('Error updating post. Please try again.');
        });
    };

    // Delete comment
    $scope.deleteComment = function(post, comment) {
        if (!confirm('Are you sure you want to delete this comment?')) return;

        $http.delete('/api/comments/' + comment.id)
            .then(function(response) {
                const index = post.comments.indexOf(comment);
                if (index > -1) {
                    post.comments.splice(index, 1);
                }
            })
            .catch(function(error) {
                console.error('Error deleting comment:', error);
                alert('Error deleting comment. Please try again.');
            });
    };

    // Initial load of posts
    $scope.getPosts();
}]);

// Add NotificationController
app.controller('NotificationController', ['$scope', '$http', '$interval', function($scope, $http, $interval) {
    $scope.notifications = [];
    $scope.unreadCount = 0;
    $scope.showNotifications = false;

    // Toggle notifications dropdown
    $scope.toggleNotifications = function(event) {
        event.stopPropagation(); // Prevent event from bubbling up
        $scope.showNotifications = !$scope.showNotifications;
    };

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if ($scope.showNotifications) {
            $scope.$apply(function() {
                $scope.showNotifications = false;
            });
        }
    });

    // Fetch notifications
    $scope.getNotifications = function() {
        $http.get('/api/notifications').then(function(response) {
            $scope.notifications = response.data;
            $scope.unreadCount = $scope.notifications.filter(n => !n.is_read).length;
        });
    };

    // Mark notification as read
    $scope.markAsRead = function(notification) {
        if (notification.is_read) return;
        
        $http.post('/api/notifications/' + notification.id + '/read').then(function() {
            notification.is_read = true;
            $scope.unreadCount = $scope.notifications.filter(n => !n.is_read).length;
        });
    };

    // Mark all as read
    $scope.markAllAsRead = function() {
        $http.post('/api/notifications/read-all').then(function() {
            $scope.notifications.forEach(n => n.is_read = true);
            $scope.unreadCount = 0;
        });
    };

    // Listen for new notifications
    window.Echo.private('notifications.' + window.Laravel.user.id)
        .listen('.NewNotification', (e) => {
            console.log('New notification received:', e);
            $scope.$apply(function() {
                $scope.notifications.unshift(e.notification);
                $scope.unreadCount++;
            });
        });

    // Initial load
    $scope.getNotifications();

    // Add auto-refresh
    $interval(function() {
        $scope.getNotifications();
    }, 2000);
}]);

app.controller('MessageController', ['$scope', '$http', function($scope, $http) {
    $scope.users = window.initialUsers;
    $scope.filteredUsers = $scope.users;
    $scope.selectedUser = null;
    $scope.messages = [];
    $scope.newMessage = '';
    $scope.currentUser = window.Laravel.user;
    $scope.searchQuery = '';

    $scope.filterUsers = function() {
        if (!$scope.searchQuery) {
            $scope.filteredUsers = $scope.users;
        } else {
            $scope.filteredUsers = $scope.users.filter(user => 
                user.name.toLowerCase().includes($scope.searchQuery.toLowerCase())
            );
        }
    };

    $scope.selectUser = function(user) {
        $scope.selectedUser = user;
        $scope.getMessages(user);
        $scope.markMessagesAsRead(user);
    };

    $scope.getMessages = function(user) {
        $http.get('/api/messages/' + user.id)
            .then(function(response) {
                $scope.messages = response.data;
                setTimeout(() => {
                    const container = document.getElementById('messages-container');
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                }, 0);
            })
            .catch(function(error) {
                console.error('Error fetching messages:', error);
            });
    };

    $scope.sendMessage = function() {
        var messageInput = document.querySelector('input[ng-model="newMessage"]');
        var messageContent = messageInput.value.trim();
        
        if (!messageContent || !$scope.selectedUser) return;

        $http.post('/api/messages', {
            receiver_id: $scope.selectedUser.id,
            content: messageContent
        })
        .then(function(response) {
            $scope.messages.push(response.data);
            messageInput.value = '';
            $scope.newMessage = '';
            setTimeout(() => {
                const container = document.getElementById('messages-container');
                container.scrollTop = container.scrollHeight;
            }, 0);
        })
        .catch(function(error) {
            console.error('Error sending message:', error);
        });
    };

    $scope.markMessagesAsRead = function(user) {
        $http.post('/api/messages/' + user.id + '/read')
            .catch(function(error) {
                console.error('Error marking messages as read:', error);
            });
    };
}]);

app.controller('UnreadMessagesController', ['$scope', '$http', function($scope, $http) {
    $scope.unreadMessageCount = 0;

    // Get initial unread count
    $scope.getUnreadCount = function() {
        $http.get('/api/messages/unread-count').then(function(response) {
            $scope.unreadMessageCount = response.data.count;
        });
    };

    // Listen for new messages
    window.Echo.private('messages.' + window.Laravel.user.id)
        .listen('NewMessage', (e) => {
            $scope.$apply(function() {
                $scope.unreadMessageCount++;
            });
        });

    // Initial load
    $scope.getUnreadCount();
}]);