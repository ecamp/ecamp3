/**
 * Created by pirmin on 13.11.14.
 */

(function() {

    var module = angular.module('ecamp.contextmenu', []);

    var pointerEvents = 'contextmenu';

    module.directive('contextmenuMenu', ['$window', '$parse', function($window, $parse){

        var $window = angular.element($window);

        return {
            restrict: 'A',
            scope: false,
            controller: ['$scope', '$attrs', function($scope, $attrs){
                var ctrl = {};

                ctrl.$element = null;
                ctrl.$isOpen = false;

                ctrl.close = function(){
                    ctrl.$element.toggleClass('open', false);
                    ctrl.$isOpen = false;
                };

                ctrl.open = function(item, event){
                    ctrl.$item = item;

                    var top = event.pageY - $window.scrollTop();
                    ctrl.$element.css('left', event.pageX + 'px');
                    ctrl.$element.css('top', top + 'px');
                    ctrl.$element.toggleClass('open', true);
                    ctrl.$isOpen = true;

                    return ctrl.$isOpen;
                };

                var closeEvents = pointerEvents + ' mousedown click';

                $window.on(closeEvents, function(event){
                    if(ctrl.$isOpen){
                        var menuClicked = jQuery.contains(ctrl.$element[0], event.target);
                        if(menuClicked){
                            if(event.type == "click") {
                                ctrl.close();
                                return true;
                            } else {
                                return false;
                            }
                        } else {
                            ctrl.close();
                            event.preventDefault();
                            event.stopPropagation();
                            return false;
                        }
                    }
                });
                $window.on('keydown', function(event){
                    if(event.keyCode == 27){
                        if(ctrl.$isOpen){
                            event.preventDefault();
                            event.stopPropagation();

                            ctrl.close();
                        }
                    }
                });

                return ctrl;
            }],
            link: function ($scope, $element, $attrs, $ctrl) {
                $ctrl.$element = $element;
                $parse($attrs.contextmenuMenu).assign($scope, $ctrl);
            }
        }
    }]);

    module.directive('contextmenuContainer', ['$parse', function($parse){
        return {
            restrict: 'A',
            scope: false,
            controller: ['$scope', '$attrs', function($scope, $attrs){
                return $parse($attrs.contextmenuContainer)($scope);
            }]
        }
    }]);

    module.directive('contextmenuItem', [function(){
        return {
            restrict: 'A',
            require: '^contextmenuContainer',
            scope: false,
            link: function($scope, $element, $attrs, contextMenuCtrl){

                var iam = $scope[($attrs.contextmenuItem)];
                $element.on(pointerEvents, function(event){
                    $scope.$apply(function () {
                        contextMenuCtrl.open(iam, event);
                    });
                    event.preventDefault();
                    event.stopPropagation();
                    return false;
                });

                $element.on('mousedown',function(event){
                    if(contextMenuCtrl.$isOpen){
                        event.preventDefault();
                        event.stopPropagation();
                        return false;
                    }

                    return true;
                });
            }
        }
    }]);

})();
