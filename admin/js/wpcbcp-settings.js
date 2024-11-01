(function () {
    var app = angular.module("wpchatbull", ['ngSanitize'], function ($httpProvider) {
        // Use x-www-form-urlencoded Content-Type
        $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';

        /**
         * The workhorse; converts an object to x-www-form-urlencoded serialization.
         * @param {Object} obj
         * @return {String}
         */
        var param = function (obj) {
            var query = '', name, value, fullSubName, subName, subValue, innerObj, i;

            for (name in obj) {
                value = obj[name];

                if (value instanceof Array) {
                    for (i = 0; i < value.length; ++i) {
                        subValue = value[i];
                        fullSubName = name + '[' + i + ']';
                        innerObj = {};
                        innerObj[fullSubName] = subValue;
                        query += param(innerObj) + '&';
                    }
                } else if (value instanceof Object) {
                    for (subName in value) {
                        subValue = value[subName];
                        fullSubName = name + '[' + subName + ']';
                        innerObj = {};
                        innerObj[fullSubName] = subValue;
                        query += param(innerObj) + '&';
                    }
                } else if (value !== undefined && value !== null)
                    query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
            }

            return query.length ? query.substr(0, query.length - 1) : query;
        };

        // Override $http service's default transformRequest
        $httpProvider.defaults.transformRequest = [function (data) {
                return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
            }];
    });

    app.constant("Setting", {
        ajax_url: cbscript.ajax_url,
        site_url: cbscript.site_url
    });

    app.controller("SettinsController", function ($http, $scope, $interval, $timeout, $log, $window, Setting) {
        $scope.notification = {message: '', errors: ''};
        $scope.labels = cbscript.labels;
        $scope.settings = angular.copy(cbscript.settings);
        $scope.show_options = $scope.settings.gaxon_wpcbcp_linked;
        $scope.change_plugin_path = false;
        $scope.status_checked = false;

        /*
         * To show errors
         * 
         * @param {array} errors
         */
        $scope.show_errors = function (errors) {
            angular.forEach(errors, function (error, key) {
                $scope.notification.errors += "<p>" + error + "</p>";
            });

            /*$timeout(function () {
             $scope.reset_notification();
             }, 3000);*/
        }

        /*
         * To show message
         * 
         * @param {String} message
         */
        $scope.show_message = function (message) {
            $scope.notification.message = "<p>" + message + "</p>";

            /*$timeout(function () {
             $scope.reset_notification();
             }, 3000);*/
        }

        $scope.reset_notification = function () {
            $scope.notification = {message: '', errors: ''};
        }

        /*
         * To install plugin
         * 
         * @param {Event} event
         */
        $scope.install_plugin = function (event) {
            event.preventDefault();
            $scope.reset_notification();

            var data = {
                action: 'wpcbcp_install_plugin',
                settings: $scope.settings
            }

            $http.post(Setting.ajax_url, data).then(function (response) {
                $log.log(response);
                if (response.data.result == 'success') {
                    $scope.settings.gaxon_wpcbcp_linked = 'yes';
                    $scope.show_message(response.data.message);
                    $window.location = response.data.plugin_url;
                } else {
                    $scope.show_errors(response.data.errors);
                }
            });
        }

        /*
         * Check plugin path and save if valid.
         * @param {type} event
         * @returns {undefined}
         */
        $scope.check_n_save = function (event) {
            event.preventDefault();
            $scope.reset_notification();

            var data = {
                action: 'wpcbcp_check_n_save',
                settings: $scope.settings
            }

            $http.post(Setting.ajax_url, data).then(function (response) {
                if (response.data.result == 'success') {
                    $scope.settings.gaxon_wpcbcp_linked = 'yes';
                    $scope.change_plugin_path = false;
                    $scope.show_message(response.data.message);
                } else {
                    $scope.show_errors(response.data.errors);
                }
            });
        }

        /*
         * To check status of plugin.
         * 
         * @returns {undefined}
         */
        $scope.chack_status = function () {
            $scope.reset_notification();
            
            var data = {
                action: 'wpcbcp_chack_status',
                settings: $scope.settings
            }

            $http.post(Setting.ajax_url, data).then(function (response) {
                if (response.data.result == 'success') {
                    if ($scope.status_checked) {
                        $scope.show_message(response.data.message);
                    }
                } else {
                    $scope.show_errors(response.data.errors);
                }

                $scope.status_checked = true;
            });
        }
        
        if ($scope.settings.gaxon_wpcbcp_linked == 'yes' && $scope.settings.gaxon_wpcbcp_chatbox_method == 'install-plugin' && $scope.settings.gaxon_wpcbcp_chatbull_dir != '') {
            $scope.chack_status();
        }

        /*
         * To save settings data 
         * 
         * @returns {undefined}
         */
        $scope.save_settings = function () {
            $scope.reset_notification();

            var data = {
                action: 'wpcbcp_save_settings',
                settings: $scope.settings
            }

            $http.post(Setting.ajax_url, data).then(function (response) {
                $log.log(response);
                if (response.data.result == 'success') {
                    $scope.show_message(response.data.message);
                    angular.merge($scope.settings, response.data.settings);
                    $scope.change_plugin_path = false;
                } else {
                    $scope.show_errors(response.data.errors);
                }
            });
        }
    });
})();