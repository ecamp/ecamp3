
function Paginator($scope, $resource, $timeout){
	$scope.numberOfPages = null;
	$scope.numberOfItems = null;
	$scope.itemsPerPage = null;
	$scope.currentPage = 1;
	$scope.items = [];
	$scope.isLoading = false;
	$scope.searchQuery = undefined;
	
	
	var resource;
	var searchTimeout = null;
	var keyTimeout = null;
	
	var loadSearchQuery = function(){
		if(searchTimeout != null){
			$timeout.cancel(searchTimeout);
			searchTimeout = null;
		}
		if(keyTimeout != null){
			$timeout.cancel(keyTimeout); 
			keyTimeout = null;
		}
		$scope.loadPage($scope.currentPage);
	};
	var searchQueryChanged = function(){
		if(searchTimeout == null){
			searchTimeout = $timeout(loadSearchQuery, 1000);
		}
		
		if(keyTimeout != null){
			$timeout.cancel(keyTimeout);
		}
		keyTimeout = $timeout(loadSearchQuery, 150);
	};
	
	$scope.init = function(url, paramDefaults){
		resource = $resource(url, paramDefaults);
		$scope.loadPage();
		
		$scope.$watch('searchQuery', searchQueryChanged);
	};
	
	$scope.loadPage = function(pageNumber){
		$scope.isLoading = true;
		
		if(angular.isUndefined(pageNumber)){ pageNumber = 1; }
		
		params = { 'page': pageNumber };
		if(angular.isNumber($scope.itemsPerPage)){
			params.limit = $scope.itemsPerPage;
		}
		if(angular.isDefined($scope.searchQuery)){
			params.search = $scope.searchQuery;
		}
		
		var list = resource.get(
			params,
			function(){
				$scope.items = list._embedded.items;
				$scope.numberOfPages = list.pages;
				$scope.numberOfItems = list.count;
				$scope.itemsPerPage = list.limit;
				$scope.currentPage = list.page;
				
				$scope.isLoading = false;
			}
		);
	};
	
	$scope.getCount = function(){
		if(angular.isNumber($scope.numberOfItems)){
			return '[ ' + $scope.numberOfItems + ' ]';
		}
		return "";
	};
	
	$scope.getPageList = function(){
		var pages = [];
		for(var i = 1; i <= $scope.numberOfPages; i++){ pages.push(i); }
		return pages;
	};
};
