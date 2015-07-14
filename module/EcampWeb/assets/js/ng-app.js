/**
 * Created by pirminmattmann on 03.08.14.
 */

(function(ecamp){

    ecamp.ngApp = angular.module('ecamp',
        [   'ngResource'
        ,   'pascalprecht.translate'
        ,   'ui.bootstrap'
        ,   'ui.slider'
        ,   'ui.select'
        ,   'ui.sortable'
        ,   'ngSanitize'
        ,   'ecamp.RemoteData'
        ,   'ecamp.RemoteMaterialData'
        ]
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
                $scope.$watch(function(){ return $element.is(":visible")}, function(visible){
                    if(visible){ $element.trigger('autosize.resize'); }
                });
                $element.autosize();
            }
        }
    }]);


    ecamp.ngApp.directive('durationMin', function(){
        function lpad(str, padString, length) {
            while (str.length < length)
                str = padString + str;
            return str;
        }

        return {
            restrict: 'A',
            require: '?ngModel',
            link: function(scope, element, attrs, ngModel) {
                if (!ngModel) return;

                ngModel.$formatters.push(function(value){
                    var duration = value || 0;
                    var hour = Math.floor(duration / 60);
                    var min = lpad((duration % 60) + "", "0", 2);
                    return hour + ":" + min;

                });

                ngModel.$parsers.push(function(value){
                    var vals = value.split(':');

                    if(vals.length == 0){
                        return null;
                    }
                    if(vals.length == 1){
                        return 1 * vals[0];
                    }
                    if(vals.length == 2){
                        return 60 * vals[0] + 1 * vals[1];
                    }

                    return undefined;
                });
            }
        };
    });


})(CNS('ecamp'));
