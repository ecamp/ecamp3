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
        ,   'ngSanitize'
        ,   'angularFileUpload'
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



    ecamp.ngApp.directive('fileread', [function(){
        return {
            scope: {
                'fileread': "="
            },
            link: function (scope, element, attributes) {

                var isSingleFile = (element.attr('multiple') == undefined);
                /*
                if(isSingleFile){
                    scope.fileread = undefined;
                } else {
                    scope.fileread = [];
                }
                */

                element.bind("change", function(changeEvent) {

                    if(isSingleFile){
                        scope.fileread = undefined;
                        var file = element.get(0).files[0];
                        var reader = new FileReader();
                        reader.onload = function(loadEvent){
                            scope.$apply(function(){
                                scope.fileread = loadEvent.target.result;
                            });
                        };
                        reader.readAsDataURL(file);

                    } else {
                        scope.fileread = [];
                        var files = element.get(0).files;
                        for(var idx = 0; idx < files.length; idx++){
                            var reader = new FileReader();
                            reader.onload = function(loadEvent){
                                scope.$apply(function(){
                                    scope.fileread[idx] = loadEvent.target.result;
                                });
                            };
                            reader.readAsDataURL(files[idx])
                        }
                    }

                });
            }
        }
    }])


})(CNS('ecamp'));
