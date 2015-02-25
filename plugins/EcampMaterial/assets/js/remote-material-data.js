
angular.module('ecamp.RemoteMaterialData', ['ecamp.RemoteData', 'angular-hal']);

angular.module('ecamp.RemoteMaterialData').factory('RemoteMaterialResource', ['RemoteResource', function(RemoteResource){

        function RemoteMaterialResource(halResource){
            if(!(this instanceof RemoteMaterialResource)){
                return new RemoteMaterialResource(halResource)
            }

            RemoteResource.apply(this, arguments);
        }

        RemoteMaterialResource.prototype = new RemoteResource();

        RemoteMaterialResource.prototype.SetHalResource = function(halResource){
            var result = RemoteResource.prototype.SetHalResource.apply(this, arguments);

            halResource.$get('lists').then(function (resource) {
            	
            	halResource.listsObjects = resource;
            	
            	/* 
            	 * extract IDs only
            	 * the ID array is used for the angular ui-select component 
            	 */
            	halResource.lists = $.map(resource,function(obj){return obj.id}); 
            });

            return result;
        };

        return RemoteMaterialResource;

    }]);
    
angular.module('ecamp.RemoteMaterialData').factory('RemoteMaterialCollection', ['RemoteCollection', 'RemoteMaterialResource', 'halClient', function(RemoteCollection, RemoteMaterialResource, halClient){

        function RemoteMaterialCollection(halResource){
            if(!(this instanceof RemoteMaterialCollection)){
                return new RemoteMaterialCollection(halResource)
            }

            RemoteCollection.apply(this, arguments);
            
            this.ElementType = RemoteMaterialResource;
            this.materialLists = new Array();
        }

        RemoteMaterialCollection.prototype = new RemoteCollection();
        
        RemoteMaterialCollection.prototype.SetListEndpoint = function(endpoint){  
            if(endpoint != null){
                return halClient.$get(endpoint)
                	.then( this.SetListsResource.bind(this) )
                	.then( this.SetListsItems.bind(this));
            }
        };
           
        RemoteMaterialCollection.prototype.SetListsResource = function(resource){
        	return resource.$get('items');
        };

        RemoteMaterialCollection.prototype.SetListsItems = function(resource){
        	this.materialLists = resource;
        };
        
        return RemoteMaterialCollection;

    }]);

angular.module('ecamp.RemoteMaterialData').directive('remoteMaterialCollection', ['RemoteMaterialCollection', function(RemoteMaterialCollection){
        return {
            restrict: 'EA',
            scope: false,

            link: function($scope, $element, $attrs, $ctrl){
                var _remoteCollection = new RemoteMaterialCollection();

                if("resourceName" in $attrs){
                	$scope[$attrs.resourceName] = _remoteCollection;
                }

                if("sortable" in $attrs) {
                    _remoteCollection.Sortable = $attrs.sortable;
                }
                
                /* 
                 * make sure that the complete material lists are loaded before the material items
                 * otherwise angular ui-select does not work properly
                 */
                _remoteCollection.SetListEndpoint($attrs.materialListsEndpoint).then(
                		
	                function(){
		                var remoteMaterialCollection = $attrs.remoteMaterialCollection;
		                if(remoteMaterialCollection in $scope){
		                    _remoteCollection.SetHalResource($scope[remoteMaterialCollection]);
		                } else {
		                    _remoteCollection.SetEndpoint(remoteMaterialCollection);
		                }
	                }
                
                );
                
                
            }
        };

    }]);

