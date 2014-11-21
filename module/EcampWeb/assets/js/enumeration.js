/**
 * Created by pirmin on 13.11.14.
 */

(function(ngApp) {

    ngApp.directive('remoteListResource', [function(){
        return {
            restrict: 'EA',
            scope: false,
            require: ['remoteListResource', '?ngModel'],

            controller: ['$scope', 'halClient', function($scope, halClient){

                var _endpoint = null;
                var _resource = null;
                var _items = [];
                var _sortable = false;


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

                function SetListResource(resource){
                    _resource = resource;

                    if(_resource){
                        _resource.$get('items').then(SetListItems, ClearListItems);
                    } else {
                        ClearListItems();
                    }
                }

                function ClearResource(){
                    _resource = null;
                }


                function SetListItems(items){
                    _items = items;
                }

                function ClearListItems(){
                    _items = [];
                }


                function AddListItem(listItem){
                    _items.push(listItem);
                }

                function RemoveListItem(listItem){
                    var idx = _items.indexOf(listItem);
                    if(idx > -1){
                        _items.splice(idx, 1);
                    }
                }


                function ListResource(){
                    Object.defineProperty(this, 'Endpoint', { get: function(){ return _endpoint; } });
                    Object.defineProperty(this, 'Resource', { get: function(){ return _resource; } });
                    Object.defineProperty(this, 'Items', { get: function(){ return _items; } });
                    Object.defineProperty(this, 'Sortable', { get: function(){ return _sortable; } });
                }


                Object.defineProperty(this, 'ListResource', { value: new ListResource() });

                Object.defineProperty(this, 'SetSortable', { value: SetSortable });
                Object.defineProperty(this, 'SetEndpoint', { value: SetEndpoint });
                Object.defineProperty(this, 'SetListResource', { value: SetListResource });

                Object.defineProperty(this, 'AddListItem', { value: AddListItem });
                Object.defineProperty(this, 'RemoveListItem', { value: RemoveListItem });
            }],

            link: function($scope, $element, $attrs, $ctrls){
                var $resource = $ctrls[0];
                var $ngModel = $ctrls[1];

                if($ngModel){
                    $ngModel.$setViewValue($resource.ListResource);
                }

                if("sortable" in $attrs) {
                    $resource.SetSortable($attrs.sortable == "" || $attrs.sortable);
                }

                var listResource = $attrs.remoteListResource;
                if(listResource in $scope){
                    $resource.SetListResource($scope[listResource]);
                } else {
                    $resource.SetEndpoint(listResource);
                }
            }
        };
    }]);


    ngApp.directive('remoteResource', [function(){
        return {
            restrict: 'EA',
            scope: false,
            require: ['remoteResource', '?ngModel'],

            controller: ['$scope', 'halClient', function($scope, halClient){

                var _form = null;
                var _owner = null;
                var _endpoint = null;
                var _resource = null;
                var _editdata = null;


                function SetForm(form){
                    _form = form;
                }

                function SetOwner(owner){
                    _owner = (typeof(owner) === "object") ? owner : null;
                }

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
                    _editdata = null;
                }

                function ClearResource(){
                    _resource = null;
                }

                function Delete(){
                    if(_resource) {
                        _resource.$del('self', null).then(Deleted);
                    }
                }

                function Deleted(){
                    if(_owner != null){
                        _owner.RemoveListItem(_resource);
                    }

                    _endpoint = null;
                    _resource = null;
                    _editdata = null;
                }

                function Edit(){
                    _editdata = {};

                    for(var attr in _resource){
                        _editdata[attr] = _resource[attr];
                    }
                }

                function Cancel(){
                    _editdata = null;
                }

                function Save(){
                    var doSave = true;

                    if(_form != null){
                        doSave = _form.is('.ng-valid');
                    }

                    if(doSave) {
                        _resource.$put('self', null, _editdata).then(SetResource);
                    }
                }

                function IsEditing(){
                    return _editdata != null;
                }


                function Resource(){
                    Object.defineProperty(this, 'Endpoint', { get: function(){ return _endpoint; } });
                    Object.defineProperty(this, 'Resource', { get: function(){ return _resource; } });
                    Object.defineProperty(this, 'EditData', { get: function(){ return _editdata; } });


                    Object.defineProperty(this, 'Delete', { value: Delete });
                    Object.defineProperty(this, 'Edit', { value: Edit });
                    Object.defineProperty(this, 'Cancel', { value: Cancel });
                    Object.defineProperty(this, 'Save', { value: Save });
                    Object.defineProperty(this, 'IsEditing', { get: IsEditing });
                }


                Object.defineProperty(this, 'Resource', { value: new Resource() });

                Object.defineProperty(this, 'SetForm', { value: SetForm });
                Object.defineProperty(this, 'SetOwner', { value: SetOwner });
                Object.defineProperty(this, 'SetEndpoint', { value: SetEndpoint });
                Object.defineProperty(this, 'SetResource', { value: SetResource });
            }],

            link: function($scope, $element, $attrs, $ctrls){
                var $resource = $ctrls[0];
                var $ngModel = $ctrls[1];

                if($ngModel){
                    $ngModel.$setViewValue($resource.Resource);
                }

                $resource.SetOwner($element.controller('remoteListResource'));

                if($element.is('form')){
                    $resource.SetForm($element);
                }

                var resource = $attrs.remoteResource;
                if(resource in $scope){
                    $resource.SetResource($scope[resource]);
                } else {
                    $resource.SetEndpoint(resource);
                }
            }
        };
    }]);




    ngApp.directive('createForm', [function(){
        return {
            restrict: 'EA',
            scope: false,
            require: ['createForm', '?ngModel'],

            controller: ['$scope', 'halClient', function($scope, halClient){

                var _form = null;
                var _owner = null;
                var _endpoint = null;
                var _createData = {};

                function SetForm(form){
                    _form = form;
                }

                function SetOwner(owner){
                    _owner = owner;
                }

                function SetEndpoint(endpoint){
                    _endpoint = endpoint;
                }

                function Cancel(){
                    _createData = {};
                }

                function Create(){
                    var doSave = true;

                    if(_form != null){
                        doSave = _form.is('.ng-valid');
                    }

                    if(doSave) {
                        halClient.$post(_endpoint, null, _createData).then(Created);
                    }
                }

                function Created(resource){
                    if(_owner != null){
                        _owner.AddListItem(resource);
                    }

                    _createData = {};
                }


                function Form(){
                    Object.defineProperty(this, 'CreateData', { get: function(){ return _createData; } });

                    Object.defineProperty(this, 'Cancel', { value: Cancel });
                    Object.defineProperty(this, 'Create', { value: Create });
                }

                Object.defineProperty(this, 'Form', { value: new Form() });

                Object.defineProperty(this, 'SetForm', { value: SetForm });
                Object.defineProperty(this, 'SetOwner', { value: SetOwner });
                Object.defineProperty(this, 'SetEndpoint', { value: SetEndpoint });

            }],

            link: function($scope, $element, $attrs, $ctrls){
                var $createForm = $ctrls[0];
                var $ngModel = $ctrls[1];

                if($ngModel){
                    $ngModel.$setViewValue($createForm.Form);
                }

                $createForm.SetEndpoint($attrs.createForm);
                $createForm.SetOwner($element.controller('remoteListResource'));

                if($element.is('form')){
                    $createForm.SetForm($element);
                }
            }

        };
    }]);



}(window.ecamp.ngApp));
