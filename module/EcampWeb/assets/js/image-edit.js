/**
 * Created by pirmin on 18.01.2015.
 */

(function(){

    var module = angular.module('ecamp-image-edit', [
        'ngFileUpload'
    ]);

    module.factory('ImageEditController', ['$http', '$upload', function($http, $upload){

        return function ($scope) {

            $scope.url = null;

            $scope.file = null;
            $scope.image = null;
            $scope.origImage = null;
            $scope.isUploading = false;

            $scope.fileReader = new FileReader();
            $scope.fileReader.onload = function (loadEvent) {
                $scope.$apply(function () {
                    $scope.image = loadEvent.target.result;

                    $scope.save();
                });
            };
            $scope.fileReader.onprogress = function (progressEvent) {
                $scope.$apply(function () {
                    $scope.loadProgress = progressEvent.loaded / progressEvent.total;
                });
            };

            $scope.$watch('file', function(file) {
                if(file) {
                    $scope.fileReader.readAsDataURL(file[0]);
                }
            });

            $scope.cancel = function() {
                $scope.image = $scope.origImage;
            };

            $scope.save = function() {
                $scope.isUploading = true;
                $upload.http({
                    method: 'PUT',
                    url: $scope.url,
                    data: $scope.image,
                    'headers': {
                        'Accept': '*/*',
                        'Content-Type': 'text/plain;charset=UTF-8'
                    }
                }).progress(function(evt){
                    console.log('progress: ' + parseInt(100.0 * evt.loaded / evt.total) + '%');

                }).success(function(){
                    $scope.isUploading = false;
                    $scope.origImage = $scope.image;

                });
            };

            $scope.clear = function() {
                $http.delete($scope.url)
                    .success(function(){
                        $scope.image = 'user';
                    });
            };

            this.Init = function(url){
                $scope.url = url;

                $http.get(url)
                    .success(function(data){
                        $scope.image = url + '?show';
                    }).error(function(){
                        $scope.image = 'user';
                    });
            }
        };
    }]);


    module.directive('userImageEdit', ['ImageEditController', function(ImageEditController){
        return {
            restrict: 'E',
            templateUrl: '/web-assets/tpl/image-edit.html',
            scope: true,

            controller: ImageEditController,

            link: function($scope, $element, $attrs, $ctrl){
                $ctrl.Init($attrs['url']);

                if($attrs['style']){
                    var img = $element.find('img');
                    var imgStyle = img.attr('style') + ';' + $attrs['style'];
                    img.attr('style', imgStyle);
                }
            }
        }

    }]);

}());