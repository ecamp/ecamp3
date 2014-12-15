/**
 * Created by pirmin on 15.12.14.
 */

(function(ngApp){

    ngApp.directive('campCollaborationOperation', [function(){
        return {
            restrict: 'E',
            templateUrl: '/web-assets/camp/campCollaborationOperation.html',
            controller: ['$scope', 'halClient', function($scope, halClient){

                function Init(camp, user){
                    var url = URI.expand('/api/v0/camps/{campId}/collaborators/{userId}', {
                        campId: camp,
                        userId: user
                    });

                    halClient.$get(url).then(CollaborationResourceLoaded);
                }

                function CollaborationResourceLoaded(collaboration){
                    $scope.collaboration = collaboration;

                    collaboration.$get('user').then(UserResourceLoaded);
                    collaboration.$get('camp').then(CampResourceLoaded);

                    $scope.role = collaboration.role;
                    $scope.status = collaboration.status;
                    $scope.description = collaboration.description;
                }

                function UserResourceLoaded(user){
                    $scope.user = user;
                }

                function CampResourceLoaded(camp){
                    $scope.camp = camp;
                }

                function HasAction(action){
                    return $scope.collaboration != null && $scope.collaboration.$has(action);
                }

                function ExecuteAction(action, query){
                    if($scope.collaboration.$has(action)){
                        var uri = new URI($scope.collaboration.$href(action));
                        uri.search(query);

                        halClient.$get(uri.toString()).then(CollaborationResourceLoaded);
                    } else {
                        throw "Unknown action [" + action + "]";
                    }
                }

                $scope.HasAction = HasAction;
                $scope.ExecuteAction = ExecuteAction;

                this.Init = Init;
            }],
            link: function($scope, $element, $attrs, $ctrl) {
                $ctrl.Init($attrs.camp, $attrs.user);
                $scope.size = $attrs.size || 'sm';
                $scope.showDesc = $attrs.showDesc || false;
            }
        }
    }])

}(window.ecamp.ngApp));
