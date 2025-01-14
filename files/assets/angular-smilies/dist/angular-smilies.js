/*!
 * Angular Smilies 1.3.0
 * Copyright 2014-2016 Damien "Mistic" Sorel (http://www.strangeplanet.fr)
 * Licensed under MIT (http://opensource.org/licenses/MIT)
 */
(function(){
    'use strict';

    var
    main = "smile",
    smilies = ["biggrin","confused","cool","cry","eek","evil","like","lol","love","mad","mrgreen","neutral","question","razz","redface","rolleyes","sad","smile","surprised","thumbdown","thumbup","twisted","wink"],
    shorts = {":D":"biggrin",":-D":"biggrin",":S":"confused",":-S":"confused",";(":"cry",";-(":"cry","OO":"eek","<3":"like","&lt;3":"like","^^":"lol",":|":"neutral",":-|":"neutral",":P":"razz",":-P":"razz",":(":"sad",":-(":"sad",":)":"smile",":-)":"smile",":O":"surprised",":-O":"surprised",";)":"wink",";-)":"wink"},

    regex = new RegExp(':(' + smilies.join('|') + '):', 'g'),
    template = '<i class="smiley-$1" title="$1"></i>',

    escapeRegExp = function(str) {
        return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
    },

    regExpForShort = function(str) {
        if (/^[a-z]+$/i.test(str)) { // use word boundaries if emoji is letters only
            return new RegExp('\\b'+ str +'\\b', 'gi');
        }
        else {
            return new RegExp(escapeRegExp(str), 'gi');
        }
    },

    templateForSmiley = function(str) {
        return template.replace('$1', str).replace('$1', str);
    },

    apply = function(input) {
        if (!input) return '';

        var output = input.replace(regex, template);

        for (var sm in shorts) {
            if (shorts.hasOwnProperty(sm)) {
                output = output.replace(regExpForShort(sm), templateForSmiley(shorts[sm]));
            }
        }

        return output;
    };


    angular.module('angular-smilies', [])
    /* smilies parser filter */
    .filter('smilies', function() {
        return apply;
    })
    /* smilies parser attribute */
    .directive('smilies', function() {
        return {
            restrict: 'A',
            scope: {
                source: '=smilies'
            },
            link: function($scope, el) {
                el.html(apply($scope.source));
            }
        };
    })
    /* smilies selector directive */
    .directive('smiliesSelector', ['$timeout', function($timeout) {
        var templateUrl;
        try {
            angular.module('ui.bootstrap.popover');
            templateUrl = 'template/smilies/button-a.html';
        }
        catch(e) {
            try {
                angular.module('mgcrea.ngStrap.popover');
                templateUrl = 'template/smilies/button-b.html';
            }
            catch(e) {
                console.error('No Popover module found');
                return {};
            }
        }

        return {
            restrict: 'A',
            templateUrl: templateUrl,
            scope: {
                source: '=smiliesSelector',
                placement: '@smiliesPlacement',
                title: '@smiliesTitle',
                keepOpen: '@smiliesKeepOpen'
            },
            link: function($scope, el) {
                $scope.smilies = smilies;

                $scope.append = function(smiley) {
                    $scope.source += ' :'+smiley+': ';

                    if ($scope.keepOpen === undefined) {
                        $timeout(function() {
                            el.children('i').triggerHandler('click'); // close the popover
                        });    
                    }
                };
            }
        };

    }])
    /* helper directive for input focusing */
    .directive('focusOnChange', function() {
        return {
            restrict: 'A',
            link: function($scope, el, attrs) {
                $scope.$watch(attrs.focusOnChange, function() {
                    el[0].focus();
                });
            }
        };
    })
    /* popover template */
    .run(['$templateCache', function($templateCache) {
        $templateCache.put('template/smilies/button-a.html',
            '<i class="smiley-'+ main +' smilies-selector" role="button" '+
                'uib-popover-template="\'template/smilies/popover-a.html\'" '+
                'popover-placement="{{!placement && \'left\' || placement}}" '+
                'popover-trigger="\'outsideClick\'"' +
                'popover-title="{{title}}" title="{{title}}" ></i>'
        );
        $templateCache.put('template/smilies/button-b.html',
            '<i class="smiley-'+ main +' smilies-selector" bs-popover '+
                'data-template="template/smilies/popover-b.html" '+
                'data-placement="{{!placement && \'left\' || placement}}" '+
                'title="{{title}}" title="{{title}}"></i>'
        );
        $templateCache.put('template/smilies/popover-a.html',
            '<div ng-model="smilies" class="smilies-selector-content">'+
              '<i role="button" class="smiley-{{smiley}}" title="{{smiley}}" ng-repeat="smiley in smilies" ng-click="append(smiley)"></i>'+
            '</div>'
        );
        $templateCache.put('template/smilies/popover-b.html',
            '<div class="popover" tabindex="-1">'+
                '<div class="arrow"></div>'+
                '<h3 class="popover-title" ng-bind-html="title" ng-show="title"></h3>'+
                '<div class="popover-content">'+
                    $templateCache.get('template/smilies/popover-a.html') +
                '</div>'+
            '</div>'
        );
    }]);

}());
