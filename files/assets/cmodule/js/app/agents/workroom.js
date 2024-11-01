(function () {
    app.controller("WorkroomController", function ($http, $scope, $interval, $timeout) {
        $scope.canned_messages = [];
        $scope.user = cmodule.user;
        $scope.visitor = {name: ''};
        $scope.agents_list = [];
        $scope.departments_list = [];
        $scope.message_stored = [];
        $scope.is_typing = false;
        $scope.is_scrollable = true;
        $scope.is_total_scrolled = false;
        $scope.last_message_id = 0;

        $scope.new_msg_indecator = false;
        $scope.visible_chat = true;
        $scope.visible_forward = false;
        $scope.showError = false;
        $scope.errors = '';
        $scope.blink_chatbox = false;
        $scope.messages = [];
        $scope.new_message = '';

        $scope.chatHeartbeatTime = 3000;
        var stop_heartbeat = undefined;

        $scope.chatboxState = '';

        $scope.$on('onRepeatLast', function (scope, element, attrs) {
            //work your magic
            if ($scope.chatboxState === '') {
                angular.element("#message").focus();
            }

            $timeout(function () {
                $scope.scroll_chat();
            }, 100);
        });

        // set chat history window height.
        $scope.set_height = function (csession) {
            //var body_height = $('body').height();
            var windowHeight = angular.element(window).outerHeight();
            if (csession.session_status != 'open' && csession.session_status != 'on-hold' && csession.session_status != 'disconnected') {
                angular.element('.chat-container-frame').css('height', windowHeight - 160);
            } else {
                angular.element('.chat-container-frame').css('height', windowHeight - 267);
            }
        }

        //get currant time
        $scope.currant_time = function () {
            $scope.miliseconds = new Date().getTime();
            var miliseconds = ($scope.miliseconds + $scope.time_difference).toString();
            $scope.message_stored.push(miliseconds);

            return miliseconds;
        }

        // get chat session
        $scope.$watch("chatSession", function (response, stored) {
            if (response != stored && ($scope.chat_session_id != response.chat_session_id || $scope.visible_area != 'workroom')) {
                $scope.fire_trigger('#formblock', 'click');
                $scope.fire_trigger('#offlineformblock', 'click');

                //hide notification
                $scope.notification.showMessage = false;
                $scope.notification.showErrors = false;
                $scope.is_scrollable = true;
                $scope.is_total_scrolled = false;
                $scope.new_message = '';

                if ($scope.typedMessages[response.chat_session_id]) {
                    $scope.new_message = $scope.typedMessages[response.chat_session_id];
                }
                var past_scrolled = 0;

                // managing chatbox height
                $scope.set_height(response.data.chat_session);

                angular.element(".chat-container-frame").mCustomScrollbar({
                    callbacks: {
                        onScrollStart: function () {
                            $scope.is_scroll_start = true;
                            past_scrolled = this.mcs.topPct;
                        },
                        onScroll: function () {
                            if (past_scrolled > this.mcs.topPct) {
                                $scope.is_scrollable = false;
                            }

                        },
                        onTotalScroll: function () {
                            $scope.is_total_scrolled = true;
                            $scope.is_scrollable = true;
                            $scope.new_msg_indecator = false;
                        }
                    }
                });

                $scope.rand_color = response.bckcolor;
                $scope.change_visible_area('workroom');
                $scope.chat_session_id = response.chat_session_id;
                $scope.messages = response.data.chatHistory;
                $scope.visitor = response.data.visitor;
                $scope.chat_session = response.data.chat_session;
                $scope.last_message_id = response.data.last_id;
                $scope.message_stored = response.data.message_stored;

                if ($scope.visitor.id) {
                    $scope.is_typing = ($scope.visitor.is_typing > 0) ? true : false;
                }

                if (angular.isDefined(stop_heartbeat)) {
                    $interval.cancel(stop_heartbeat);
                    stop_heartbeat = undefined;
                }

                if ($scope.chat_session.session_status == 'open' || $scope.chat_session.session_status == 'on-hold' || $scope.chat_session.session_status == 'disconnected') {
                    $scope.start_chat();
                }
            }
        });

        // start chatting.
        $scope.start_chat = function () {
            $scope.chatHeartbeat();
            stop_heartbeat = $interval(function () {
                $scope.chatHeartbeat();
            }, $scope.chatHeartbeatTime);
        }

        //chatHeartbeat
        $scope.chatHeartbeat = function () {
            var old_chat_session = $scope.chat_session;
            var typing = ($scope.new_message) ? 1 : 0;
            $http.post(site_url + "?d=agents&c=chat&m=chatHeartbeat&chat_session_id=" + $scope.chat_session_id + '&last_message_id=' + $scope.last_message_id + '&typing=' + typing).success(function (response) {
                if (response.result == 'success') {
                    $scope.visitor = response.visitor;

                    if ($scope.visitor.id) {
                        $scope.is_typing = ($scope.visitor.is_typing > 0) ? true : false;
                    }

                    if (old_chat_session.session_status != 'closed' && response.chat_session.session_status == 'closed') {
                        var closed_message = $scope.visitor.name + " left chat session.";
                        $scope.notifySidebarAlert(closed_message, 'alert-success');
                        $timeout(function () {
                            //$scope.fire_trigger('#tab-chat-history', 'click');
                            if ($scope.recent_chats.length > 0) {
                                var firstUser = $scope.recent_chats[0];
                                $scope.fire_trigger('#chat-session-' + firstUser.id, 'click');
                                $scope.change_visible_area('workroom');
                            } else {
                                $scope.fire_trigger('#link-new-requests', 'click');
                            }
                        }, 3000);
                    }

                    $scope.chat_session = response.chat_session;
                    $scope.last_message_id = response.last_id;

                    angular.forEach(response.chatMessagesData, function (row, key) {
                        if ($.inArray(row.sort_order, $scope.message_stored) == -1 && $.inArray(row.id, $scope.message_stored) == -1) {
                            $scope.messages.push(row);
                            $scope.message_stored.push(row.id);

                            if ($scope.is_scrollable) {
                                $scope.new_msg_indecator = false;
                            } else {
                                $scope.new_msg_indecator = true;
                            }

                            if (row.sender_id === $scope.visitor.id) {
                                $scope.playSound = true;
                                $scope.blink_chatbox = true;
                            }
                        }
                        //$scope.blink_chatbox = false;
                    });

                    if ($scope.playSound) {
                        $scope.play();
                        $scope.playSound = false;
                    }

                    if (response.chatMessagesData.length > 0) {
                        $scope.scroll_chat();
                    }
                } else if (response.result == 'failed') {
                    $scope.displayError(response);
                }
            });
        }

        // send messae to visitor
        $scope.submit_message = function (event) {
            if (event.keyCode == 13 && $scope.new_message) {
                if (!event.shiftKey) {
                    $scope.send_message(event);
                }
            } else if (event.keyCode == 13) {
                event.preventDefault();
            }
        }

        // sending new message to visitor
        $scope.send_message = function (event) {
            event.preventDefault();
            if ($scope.new_message) {
                if (!$scope.new_msg_indecator && $scope.is_scroll_start) {
                    $scope.is_scrollable = true;
                }

                //prepare json data
                var message_data = {
                    name: $scope.user.name,
                    chat_session_id: $scope.chat_session_id,
                    chat_message: $scope.new_message,
                    sort_order: $scope.currant_time(),
                    message_status: 'unread',
                    sender_id: $scope.user.id,
                    class: ''
                };

                $scope.new_message = '';
                $scope.messages.push(message_data);
                $scope.scroll_chat();
                $http.post(site_url + "?d=agents&c=chat&m=send", message_data).success(function (response) {
                    if (response.result == 'success') {
                        //$scope.messages.push(response.message_row);
                        //$scope.new_message = '';
                        //$scope.scroll_chat();
                    } else if (response.result == 'failed') {
                        $scope.displayError(response);
                        $scope.new_message = message_data.chat_message;
                    }

                    $scope.scroll_chat();
                });
            }
        }

        // close chat 
        $scope.end_chat = function (event) {
            event.preventDefault();

            $http.post(site_url + "?d=agents&c=chat&m=end&chat_session_id=" + $scope.chat_session_id).success(function (response) {
                if (response.result == 'success') {
                    $scope.chat_session = response.chat_session;
                    $timeout(function () {
                        $scope.fire_trigger('#tab-chat-history', 'click');
                    }, 2000);
                } else if (response.result == 'failed') {
                    $scope.displayError(response);
                }
            });
        }

        $scope.scroll_chat = function () {
            if ($scope.is_scrollable) {
                angular.element("#message_box").mCustomScrollbar('scrollTo', 'bottom', {
                    scrollInertia: 100,
                    timeout: 10
                });
            }
        }

        $scope.displayError = function (data) {
            $scope.notification.showErrors = true;
            $scope.notification.errors = data.errors;
        }
    });

    app.directive('onLastRepeat', function () {
        return function (scope, element, attrs) {
            if (scope.$last) {
                setTimeout(function () {
                    scope.$emit('onRepeatLast', element, attrs);
                }, 1);
            }
        };
    });

    /*
     This directive allows us to pass a function in on an enter key to do what we want.
     */
    app.directive('ngEnter', function () {
        return function (scope, element, attrs) {
            element.bind("keydown keypress", function (event) {
                if (event.which === 13) {
                    scope.$apply(function () {
                        scope.$eval(attrs.ngEnter);
                    });

                    event.preventDefault();
                }
            });
        };
    }).directive('enterSubmit', function () {
        return {
            restrict: 'A',
            link: function (scope, elem, attrs) {

                elem.bind('keydown', function (event) {
                    var code = event.keyCode || event.which;

                    if (code === 13) {
                        if (!event.shiftKey) {
                            event.preventDefault();
                            scope.$apply(attrs.enterSubmit);
                        }
                    }
                });
            }
        }
    });
})();