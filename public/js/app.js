// public/js/app.js
var app = angular.module('socialApp', []) || angular.module('socialApp');

app.config(['$interpolateProvider', function($interpolateProvider) {
    $interpolateProvider.startSymbol('[[');
    $interpolateProvider.endSymbol(']]');
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
app.controller('NotificationController', ['$scope', '$http', function($scope, $http) {
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
        .listen('NewNotification', (e) => {
            console.log('New notification received:', e);
            $scope.$apply(function() {
                // Add notification type class for different styling if needed
                e.notification.typeClass = {
                    'post': 'bg-green-50 dark:bg-green-900/20',
                    'like': 'bg-blue-50 dark:bg-blue-900/20',
                    'comment': 'bg-purple-50 dark:bg-purple-900/20'
                }[e.notification.type] || '';
                
                $scope.notifications.unshift(e.notification);
                $scope.unreadCount++;
            });
        });

    // Initial load
    $scope.getNotifications();
}]);