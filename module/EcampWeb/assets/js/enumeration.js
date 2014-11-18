/**
 * Created by pirmin on 13.11.14.
 */

(function(ngApp) {

    ngApp.directive('remoteListResource', [function(){
        return {
            restrict: 'EA',
            scope: false,

            controller: ['$scope', 'halClient', function($scope, halClient){

                var _sortable = false;
                var _endpoint = null;
                var _listResource = null;
                var _listItems = [];

                function SetSortable(sortable){
                    _sortable = sortable;
                }

                function SetEndpoint(endpoint){
                    _endpoint = endpoint;

                    if(_endpoint) {
                        halClient.$get(_endpoint).then(SetListResource, ClearResource);
                    } else {
                        ClearResource();
                    }
                }

                function SetListResource(listResource){
                    _listResource = listResource;

                    if(_listResource){
                        _listResource.$get('items').then(SetListItems, ClearListItems);
                    } else {
                        ClearListItems();
                    }
                }


                function SetListItems(listItems){
                    _listItems = listItems;
                }

                function ClearResource(){
                    _listResource = null;
                }

                function ClearListItems(){
                    _listItems = [];
                }

                function RemoveListItem(listItem){
                    var idx = _listItems.indexOf(listItem);
                    if(idx > -1){
                        _listItems.splice(idx, 1);
                    }
                }


                Object.defineProperty(this, 'SetSortable', { value: SetSortable });
                Object.defineProperty(this, 'SetEndpoint', { value: SetEndpoint });
                Object.defineProperty(this, 'SetListResource', { value: SetListResource });

                Object.defineProperty($scope, 'Sortable', { get: function(){ return _sortable; } });
                Object.defineProperty($scope, 'Endpoint', { get: function(){ return _endpoint; } });
                Object.defineProperty($scope, 'ListResource', { get: function(){ return _listResource; } });
                Object.defineProperty($scope, 'ListItems', { get: function(){ return _listItems; } });
                Object.defineProperty($scope, 'RemoveListItem', { value: RemoveListItem });

            }],

            link: function($scope, $element, $attrs, $ctrl){
                if("sortable" in $attrs) {
                    $ctrl.SetSortable($attrs.sortable == "" || $attrs.sortable);
                }

                var listResource = $attrs.remoteListResource;
                if(listResource in $scope){
                    $ctrl.SetListResource($scope[listResource]);
                } else {
                    $ctrl.SetEndpoint(listResource);
                }
            }
        };
    }]);


    ngApp.directive('remoteResource', [function(){
        return {
            restrict: 'EA',
            scope: false,

            controller: ['$scope', 'halClient', function($scope, halClient){

                var _endpoint = null;
                var _resource = null;
                var _editData = null;

                function SetEndpoint(endpoint){
                    _endpoint = endpoint;

                    if(_endpoint) {
                        halClient.$get(_endpoint).then(SetResource, ClearResource);
                    } else {
                        ClearResource();
                    }
                }

                function SetResource(resource){
                    _resource = resource;
                    _editData = null;
                }

                function ClearResource(){
                    _resource = null;
                }


                function Delete(){
                    _resource.$del('self', null).then(Deleted);
                }

                function Deleted(){
                    if(typeof($scope.RemoveListItem) === "function"){
                        $scope.RemoveListItem(_resource);
                    }

                    _endpoint = null;
                    _resource = null;
                    _editData = null;
                }

                function Edit(){
                    _editData = {};

                    for(var attr in _resource){
                        _editData[attr] = _resource[attr];
                    }
                }

                function Cancel(){
                    _editData = null;
                }

                function Save(){
                    _resource.$put('self', null, _editData).then(SetResource);
                }

                function IsEditing(){
                    return _editData != null;
                }


                Object.defineProperty(this, 'SetEndpoint', { value: SetEndpoint });
                Object.defineProperty(this, 'SetResource', { value: SetResource });

                Object.defineProperty($scope, 'Endpoint', { get: function(){ return _endpoint; } });
                Object.defineProperty($scope, 'Resource', { get: function(){ return _resource; } });
                Object.defineProperty($scope, 'EditData', { get: function(){ return _editData; } });

                Object.defineProperty($scope, 'Delete', { value: Delete });
                Object.defineProperty($scope, 'Edit', { value: Edit });
                Object.defineProperty($scope, 'Cancel', { value: Cancel });
                Object.defineProperty($scope, 'Save', { value: Save });
                Object.defineProperty($scope, 'IsEditing', { get: IsEditing });
            }],

            link: function($scope, $element, $attrs, $ctrl){

                var resource = $attrs.remoteResource;
                if(resource in $scope){
                    $ctrl.SetResource($scope[resource]);
                } else {
                    $ctrl.SetEndpoint(resource);
                }
            }
        };
    }]);


    ngApp.directive('createForm', [function(){
        return {
            restrict: 'EA',
            require: '^remoteListResource',
            scope: {
                id: "="
            },

            link: function($scope, $element, $attrs, $ctrl){

            }

        };
    }]);

}(window.ecamp.ngApp));
