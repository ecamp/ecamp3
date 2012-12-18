
function TestCtrl($scope, $http){
	
	loadUrl = '//api.ecamp3.dev/profil/get';
	saveUrl = '//api.ecamp3.dev/profil/update';
	
	function updateProfile(data){
		$scope.$apply($scope.profile = data.response);
	};
	
	function getProfile(){
		return $scope.profile;
	};
	
	$scope.save = function(){
		$.ajax(saveUrl, { type: "POST", data: getProfile(), xhrFields:{ withCredentials:true }, dataType: 'json' }).success(updateProfile);
		//$http.get(saveUrl, { data: getProfile, withCredentials: true });
	};
	
	$.ajax(loadUrl, { xhrFields:{ withCredentials:true }, dataType: 'json' }).success(updateProfile);
	//$http.get(loadUrl, { withCredentials: true }).success(updateProfile);
}