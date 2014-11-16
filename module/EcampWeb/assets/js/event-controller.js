/**
 * Created by pirmin on 16.11.14.
 */

(function(ngApp){

    ngApp.directive('eventPlugins', [function(){
        return {
            restrict: 'E',
            controller: ['$scope', function($scope){

                $scope.plugins = [];

                function RegisterPlugin(plugin){
                    $scope.plugins.push(plugin);
                }

                Object.defineProperty(this, 'RegisterPlugin', { value: RegisterPlugin });
            }]
        };
    }]);


    ngApp.directive('linearPluginContainer', [function(){
        return {
            restrict: 'E',
            scope: true,

            controller: ['$scope', '$element', '$timeout', '$http', '$compile',
            function($scope, $element, $timeout, $http, $compile){

                var _name = null;
                var _minEventPlugin = 0;
                var _maxEventPlugin = null;
                var _createUrl = null;
                var _deleteUrl = null;

                function CountEventPlugin(){
                    return $element.find('div[linear-plugin-container-item]').length;
                }

                function Create(){
                    if(CountEventPlugin() >= $scope.MaxEventPlugin){
                        throw "Can not create more Plugins of this typ";
                    }

                    if(!_createUrl){
                        throw "Now Create-Url defined, for this Plugin";
                    }

                    $http.get(_createUrl).success(InsertPlugin);
                }

                function InsertPlugin(data){
                    var element = $(data);
                    $element.append(element);
                    $compile(element)($scope);

                    element.fadeOut(0);
                    element.slideDown(500);
                }

                function Delete(eventPluginId){
                    if(CountEventPlugin() <= $scope.MinEventPlugin){
                        throw "Can not delete Plugin";
                    }

                    var item = $element.find('div[linear-plugin-container-item][id=' + eventPluginId + ']');

                    $http.get(_deleteUrl + eventPluginId).success(function(){
                        item.slideUp(500, function(){
                            $timeout(function(){ item.remove(); });
                        });
                    });
                }

                function Deletable(eventPluginId){
                    return CountEventPlugin() > $scope.MinEventPlugin;
                }

                Object.defineProperty($scope, 'Name', {
                    get: function(){ return _name; },
                    set: function(val){ _name = val; }
                });
                Object.defineProperty($scope, 'MinEventPlugin', {
                    get: function(){ return _minEventPlugin; },
                    set: function(val){ _minEventPlugin = val; }
                });
                Object.defineProperty($scope, 'MaxEventPlugin', {
                    get: function(){ return _maxEventPlugin; },
                    set: function(val){ _maxEventPlugin = val; }
                });
                Object.defineProperty($scope, 'CreateUrl', {
                    get: function(){ return _createUrl },
                    set: function(val){ _createUrl = val; }
                });
                Object.defineProperty($scope, 'DeleteUrl', {
                    get: function(){ return _deleteUrl },
                    set: function(val){ _deleteUrl = val; }
                });

                Object.defineProperty($scope, 'CountEventPlugin', { get: CountEventPlugin });
                Object.defineProperty($scope, 'Create', { value: Create });

                Object.defineProperty(this, 'Delete', { value: Delete });
                Object.defineProperty(this, 'Deletable', { value: Deletable });
            }],

            link: function ($scope, $element, $attrs, $ctrl) {
                $scope.Name = $attrs.name;
                $scope.MinEventPlugin = $attrs.minItems;
                $scope.MaxEventPlugin = $attrs.maxItems;
                $scope.CreateUrl = $attrs.create;
                $scope.DeleteUrl = $attrs.delete;

                var eventPluginsController = $element.controller('eventPlugins');

                if(eventPluginsController) {
                    eventPluginsController.RegisterPlugin($scope);
                }
            }
        };
    }]);

    ngApp.directive('linearPluginContainerItem', [function(){
        return {
            restrict: 'EA',
            require: '^linearPluginContainer',
            scope: true,

            link: function($scope, $element, $attrs, $ctrl) {

                Object.defineProperty($scope, 'Delete', {
                    value: function(){
                        $ctrl.Delete($attrs.id);
                    }
                });

                Object.defineProperty($scope, 'Deletable', {
                    value: function(){
                        return $ctrl.Deletable($attrs.id);
                    }
                });
            }
        };
    }]);


    ngApp.directive('tabbedPluginContainer', [function(){
        return {
            restrict: 'E',
            scope: true,

            controller: ['$scope', '$element', '$timeout', '$http', '$compile',
                function($scope, $element, $timeout, $http, $compile){

                    var _name = null;
                    var _minEventPlugin = 0;
                    var _maxEventPlugin = null;
                    var _createUrl = null;
                    var _deleteUrl = null;
                    var _eventPlugins = [];

                    function RegisterEventPlugin(eventPlugin){
                        _eventPlugins.push(eventPlugin);

                        $timeout(function(){
                            $element.find('.nav-tabs li:last a').tab('show');
                        });
                    }

                    function RemoveEventPlugin(eventPluginId){
                        for(var idx = _eventPlugins.length - 1; idx >= 0; idx--){
                            if(_eventPlugins[idx].id == eventPluginId){
                                _eventPlugins.splice(idx, 1);

                                $timeout(function() {
                                    var liIdx = Math.min(_eventPlugins.length - 1, idx);
                                    $element.find('.nav-tabs li:eq(' + liIdx + ') a').tab('show');
                                });
                            }
                        }
                    }

                    function GetEventPlugins(){
                        return _eventPlugins;
                    }

                    function CountEventPlugin(){
                        return $element.find('div[tabbed-plugin-container-item]').length;
                    }

                    function Create(){
                        if(CountEventPlugin() >= $scope.MaxEventPlugin){
                            throw "Can not create more Plugins fo this typ";
                        }

                        if(!_createUrl){
                            throw "Now Create-Url defined, for this Plugin";
                        }

                        $http.get(_createUrl).success(InsertPlugin);
                    }

                    function InsertPlugin(data){
                        var element = $(data);
                        $element.find('.tab-content').append(element);
                        $compile(element)($scope);

                        element.children('div').fadeOut(0);
                        element.children('div').slideDown(500);
                    }

                    function Delete(eventPluginId){
                        var item = $('div[tabbed-plugin-container-item][id=' + eventPluginId + ']');

                        $http.get(_deleteUrl + eventPluginId).success(function(){
                            item.children('div').fadeOut(500, function(){
                                $timeout(function(){ item.remove(); });
                                RemoveEventPlugin(eventPluginId);
                            });
                        });
                    }

                    Object.defineProperty($scope, 'Name', {
                        get: function(){ return _name; },
                        set: function(val){ _name = val; }
                    });
                    Object.defineProperty($scope, 'MinEventPlugin', {
                        get: function(){ return _minEventPlugin; },
                        set: function(val){ _minEventPlugin = val; }
                    });
                    Object.defineProperty($scope, 'MaxEventPlugin', {
                        get: function(){ return _maxEventPlugin; },
                        set: function(val){ _maxEventPlugin = val; }
                    });
                    Object.defineProperty($scope, 'CreateUrl', {
                        get: function(){ return _createUrl },
                        set: function(val){ _createUrl = val; }
                    });
                    Object.defineProperty($scope, 'DeleteUrl', {
                        get: function(){ return _deleteUrl },
                        set: function(val){ _deleteUrl = val; }
                    });

                    Object.defineProperty($scope, 'CountEventPlugin', { get: CountEventPlugin });
                    Object.defineProperty($scope, 'EventPlugins', { get: GetEventPlugins });
                    Object.defineProperty($scope, 'Create', { value: Create });
                    Object.defineProperty($scope, 'Delete', { value: Delete });

                    Object.defineProperty(this, 'RegisterEventPlugin', { value: RegisterEventPlugin });
                }],

            link: function ($scope, $element, $attrs, $ctrl) {
                $scope.Name = $attrs.name;
                $scope.MinEventPlugin = $attrs.minItems;
                $scope.MaxEventPlugin = $attrs.maxItems;
                $scope.CreateUrl = $attrs.create;
                $scope.DeleteUrl = $attrs.delete;

                var eventPluginsController = $element.controller('eventPlugins');

                if(eventPluginsController) {
                    eventPluginsController.RegisterPlugin($scope);
                }
            }
        };
    }]);

    ngApp.directive('tabbedPluginContainerItem', [function(){
        return {
            restrict: 'EA',
            require: '^tabbedPluginContainer',
            scope: {
                id: '@',
                name: '@'
            },
            link: function($scope, $element, $attrs, $ctrl) {
                $ctrl.RegisterEventPlugin($scope);
            }
        };
    }]);

}(window.ecamp.ngApp));
