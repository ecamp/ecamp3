/**
 * Created by pirminmattmann on 03.08.14.
 */

(function(ecamp){

    ecamp.ngApp = angular.module('ecamp',
        ['ngResource', /*'ngAnimate',*/ 'ui.bootstrap', 'ui.slider', 'angular-hal']
    );


    ecamp.ngApp.filter('floor', function() {
        return function(input) {
            return Math.floor(input);
        };
    });


    ecamp.ngApp.directive('autosize', [function(){
        return {
            restrict: 'A',
            scope: false,
            link: function($scope, $element){
                $element.autosize();
            }
        }
    }])



})(CNS('ecamp'));
