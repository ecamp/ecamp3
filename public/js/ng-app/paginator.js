
function Paginator($scope, $resource){
	$scope.numberOfPages = null;
	$scope.numberOfItems = null;
	$scope.itemsPerPage = null;
	$scope.currentPage = null;
	$scope.items = [];
	$scope.resource = null;
	$scope.isLoading = false;
	
	$scope.init = function(url, paramDefaults){
		$scope.resource = $resource(url, paramDefaults);
		$scope.loadPage();
	};
	
	$scope.loadPage = function(pageNumber){
		$scope.isLoading = true;
		
		if(angular.isUndefined(pageNumber)){ pageNumber = 1; }
		
		params = { 'page': pageNumber };
		if(angular.isNumber($scope.itemsPerPage)){
			params.limit = $scope.itemsPerPage;
		}
		
		var list = $scope.resource.get(
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
