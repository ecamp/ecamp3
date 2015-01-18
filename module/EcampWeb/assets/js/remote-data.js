/**
 * Created by pirmin on 13.11.14.
 */

angular.module('ecamp.RemoteData', ['angular-hal']);

angular.module('ecamp.RemoteData').factory('RemoteResource', ['$timeout', 'halClient', function($timeout, halClient){

        function RemoteResource(halResource){
            if(!(this instanceof RemoteResource)){
                return new RemoteResource(halResource);
            }

            this.Form = null;
            this.Owner = null;
            this.HalResource = null;
            this.EditData = null;
            this.EditMode = false;

            this.SetHalResource(halResource);
        }

        RemoteResource.prototype.SetEndpoint = function(endpoint){
            if(endpoint){
                halClient.$get(endpoint).then(
                    this.SetHalResource.bind(this),
                    this.ClearHalResource.bind(this)
                );
            } else {
                this.ClearHalResource();
            }
        };

        RemoteResource.prototype.SetHalResource = function(halResource){
            this.HalResource = halResource;
            this.EditData = null;
            this.EditMode = false;
        };

        RemoteResource.prototype.ClearHalResource = function(){
            this.HalResource = null;
        };

        RemoteResource.prototype.Delete = function(){
            if(this.HalResource != null){
                return this.HalResource.$del('self', null).then(function(){
                    this.SetHalResource(null);
                }.bind(this));
            }
        };

        RemoteResource.prototype.Edit = function(){
            this.EditData = {};
            this.EditMode = true;

            for(var attr in this.HalResource){
                this.EditData[attr] = this.HalResource[attr];
            }

            if(this.Form != null){
                var form = this.Form;
                $timeout(function(){ form.find('.form-control:first').focus(); });
            }
        };

        RemoteResource.prototype.IsEditing = function(){
            return this.EditMode;
        };

        RemoteResource.prototype.Cancel = function(){
            this.EditData = null;
            this.EditMode = false;
        };

        RemoteResource.prototype.Save = function(){
            var doSave = true;

            if(this.Form != null){
                doSave = this.Form.is('.ng-valid');
            }

            if(doSave){
                this.SaveData(this.EditData);
            }
        };

        RemoteResource.prototype.SaveData = function(data){
            return this.HalResource.$put('self', null, data).then(this.SetHalResource.bind(this));
        };

        RemoteResource.prototype.KeyHandler = function(event){
            if(event.keyCode == 27 /* ESC */){
                event.preventDefault();
                this.Cancel();
                return;
            }

            if(event.ctrlKey && event.keyCode == 83 /* Ctrl-S */){
                event.preventDefault();
                this.Save();
                return;
            }

            if(event.metaKey && event.keyCode == 83 /* Meta-S */){
                event.preventDefault();
                this.Save();
                return;
            }
        };

        return RemoteResource;
    }]);


angular.module('ecamp.RemoteData').factory('RemoteCollection', ['$timeout', '$q', 'halClient', 'RemoteResource', function($timeout, $q, halClient, RemoteResource){

        function RemoteCollection(halResource){
            if(!(this instanceof RemoteCollection)){
                return new RemoteCollection(halResource);
            }

            this.Endpoint = null;
            this.HalResource = null;
            this.Items = [];

            this.Sortable = false;
            this.ElementType = RemoteResource;

            if(halResource != null){
                this.SetHalResource(halResource);
            }
        }

        RemoteCollection.prototype.SetEndpoint = function(endpoint){
            this.Endpoint = endpoint;

            if(endpoint != null){
                halClient.$get(endpoint).then(
                    this.SetHalResource.bind(this),
                    this.ClearHalResource.bind(this)
                );
            } else {
                this.ClearHalResource();
            }
        };

        RemoteCollection.prototype.SetHalResource = function(halResource){
            this.HalResource = halResource;

            if(halResource != null){
                halResource.$get('items').then(
                    this.SetItems.bind(this),
                    this.ClearItems.bind(this)
                );
            } else {
                this.ClearItems();
            }
        };

        RemoteCollection.prototype.ClearHalResource = function(){
            this.HalResource = null;
            this.ClearItems();
        };

        RemoteCollection.prototype.SetItems = function(items){
            this.Items = items.map(this.ElementType);

            this.SortItems();
        };

        RemoteCollection.prototype.AddItem = function(item){
            this.Items.push(this.ElementType(item));

            this.SortItems();
        };

        RemoteCollection.prototype.ClearItems = function(){
            this.Items = [];
        };

        RemoteCollection.prototype.SortItems = function(){
            if(this.Sortable){
                this.Items.sort(this.CompareItem.bind(this));
            }
        };

        RemoteCollection.prototype.CompareItem = function(a, b){
            if(this.Sortable){
                var keyA = a.HalResource[this.Sortable];
                var keyB = b.HalResource[this.Sortable];

                if(keyA < keyB) return -1;
                if(keyA > keyB) return 1;
            }
            return 0;
        };


        RemoteCollection.prototype.MoveUp = function(item){
            if(this.Sortable){
                var idx = this.Items.indexOf(item);
                var pos = item.HalResource[this.Sortable];

                if(idx > 0){
                    var prev = this.Items[idx - 1];

                    $q.all([
                        item.SaveData(this.GetMoveData(pos - 1)),
                        prev.SaveData(this.GetMoveData(pos))
                    ]).then(
                        this.SortItems.bind(this)
                    );

                } else {
                    throw "Item is already the first one"
                }
            } else {
                throw "List is not sortable";
            }
        };

        RemoteCollection.prototype.MoveDown = function(item){
            if(this.Sortable){
                var idx = this.Items.indexOf(item);
                var pos = item.HalResource[this.Sortable];

                if(idx < (this.Items.length - 1)){
                    var next = this.Items[idx + 1];

                    $q.all([
                        item.SaveData(this.GetMoveData(pos + 1)),
                        next.SaveData(this.GetMoveData(pos))
                    ]).then(
                        this.SortItems.bind(this)
                    );

                } else {
                    throw "Item is already the last one"
                }
            } else {
                throw "List is not sortable";
            }
        };

        RemoteCollection.prototype.GetMoveData = function(pos){
            var data = {};
            data[this.Sortable] = pos;
            return data;
        };

        RemoteCollection.prototype.Create = function(createResource){
            if(createResource != null){
                createResource.Create().then(this.AddItem.bind(this));
            }
        };

        RemoteCollection.prototype.Delete = function(item){
            if(item != null){
                var items = this.Items;

                item.Delete().then(function(){
                    var idx = items.indexOf(item);
                    items.splice(idx, 1);
                });
            }
        };

        return RemoteCollection;
    }]);

angular.module('ecamp.RemoteData').factory('RemoteCreateResource', ['$timeout', '$q', 'halClient', function($timeout, $q, halClient){

        function RemoteCreateResource(){
            if(!(this instanceof RemoteCreateResource)) {
                return new RemoteCreateResource();
            }

            this.Endpoint = null;
            this.Data = null;
            this.Form = null;
        }

        RemoteCreateResource.prototype.Show = function(){
            this.Data = {};

            if(this.Form != null) {
                var form = this.Form;
                $timeout(function(){ form.find('.form-control:first').focus(); });
            }
        };

        RemoteCreateResource.prototype.IsVisible = function(){
            return (this.Data != null);
        };

        RemoteCreateResource.prototype.Create = function(){
            var doSave = true;
            var defer = $q.defer();
            defer.promise.then(this.Created.bind(this));

            if(this.Form != null){
                doSave = this.Form.is('.ng-valid');
            }

            if(doSave) {
                halClient.$post(this.Endpoint, null, this.Data).then(defer.resolve);
            } else {
                defer.reject();
            }

            return defer.promise;
        };

        RemoteCreateResource.prototype.Created = function(){
            this.Data = null;
        };

        RemoteCreateResource.prototype.KeyHandler = function(event){
            if(event.keyCode == 27 /* ESC */){
                event.preventDefault();
                this.Cancel();
                return;
            }

            if(navigator.platform.substr(0, 3) == "Mac"){
                if(event.metaKey && event.keyCode == 83 /* Meta-S */){
                    event.preventDefault();
                    this.Create();
                    return;
                }

            } else {
                if(event.ctrlKey && event.keyCode == 83 /* Ctrl-S */){
                    event.preventDefault();
                    this.Create();
                    return;
                }
            }
        };

        return RemoteCreateResource;

    }]);


angular.module('ecamp.RemoteData').directive('remoteResource', ['RemoteResource', function(RemoteResource){
        return {
            restrict: 'EA',
            scope: false,

            link: function($scope, $element, $attrs, $ctrl){
                var _remoteResource = null;

                var remoteResource = $attrs.remoteResource;
                if(remoteResource in $scope){
                    _remoteResource = $scope[remoteResource];
                } else {
                    _remoteResource = new RemoteResource();
                    _remoteResource.SetEndpoint(remoteResource);
                }

                if("resourceName" in $attrs){
                	$scope[$attrs.resourceName] = _remoteResource;
                }
                
                if($element.is('form')){
                    _remoteResource.Form = $element;
                }
            }
        };
    }]);

angular.module('ecamp.RemoteData').directive('remoteCollection', ['RemoteCollection', function(RemoteCollection){
        return {
            restrict: 'EA',
            scope: false,

            link: function($scope, $element, $attrs, $ctrl){
                var _remoteCollection = new RemoteCollection();

                if("resourceName" in $attrs){
                	$scope[$attrs.resourceName] = _remoteCollection;
                }

                if("sortable" in $attrs) {
                    _remoteCollection.Sortable = $attrs.sortable;
                }

                var remoteCollection = $attrs.remoteCollection;
                if(remoteCollection in $scope){
                    _remoteCollection.SetHalResource($scope[remoteCollection]);
                } else {
                    _remoteCollection.SetEndpoint(remoteCollection);
                }
            }
        };
    }]);

angular.module('ecamp.RemoteData').directive('remoteCreateResource', ['RemoteCreateResource', function(RemoteCreateResource){
        return {
            restrict: 'EA',
            scope: false,

            link: function($scope, $element, $attrs, $ctrl){
                var _remoteCreateResource = new RemoteCreateResource();
                _remoteCreateResource.Endpoint = $attrs.remoteCreateResource;

                
                if("resourceName" in $attrs){
                	$scope[$attrs.resourceName] = _remoteCreateResource;
                }

                if($element.is('form')){
                    _remoteCreateResource.Form = $element;
                }
            }
        };
    }]);
