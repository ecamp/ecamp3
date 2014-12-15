/**
 * Created by pirmin on 08.12.14.
 */

(function(ngApp){

    ngApp.directive('groupMembershipOperation', [function(){
        return {
            restrict: 'E',
            templateUrl: '/web-assets/group/groupMembershipOperation.html',
            controller: ['$scope', 'halClient', function($scope, halClient){

                function Init(group, user){
                    var url = URI.expand('/api/v0/groups/{groupId}/members/{userId}', {
                        groupId: group,
                        userId: user
                    });

                    halClient.$get(url).then(MembershipResourceLoaded);
                }

                function MembershipResourceLoaded(membership){
                    $scope.membership = membership;

                    membership.$get('user').then(UserResourceLoaded);
                    membership.$get('group').then(GroupResourceLoaded);

                    $scope.role = membership.role;
                    $scope.status = membership.status;
                    $scope.description = membership.description;
                }

                function UserResourceLoaded(user){
                    $scope.user = user;
                }

                function GroupResourceLoaded(group){
                    $scope.group = group;
                }

                function HasAction(action){
                    return $scope.membership != null && $scope.membership.$has(action);
                }

                function ExecuteAction(action, query){
                    if($scope.membership.$has(action)){
                        var uri = new URI($scope.membership.$href(action));
                        uri.search(query);

                        halClient.$get(uri.toString()).then(MembershipResourceLoaded);
                    } else {
                        throw "Unknown action [" + action + "]";
                    }
                }

                $scope.HasAction = HasAction;
                $scope.ExecuteAction = ExecuteAction;

                this.Init = Init;
            }],
            link: function($scope, $element, $attrs, $ctrl) {
                $ctrl.Init($attrs.group, $attrs.user);
                $scope.size = $attrs.size || 'sm';
                $scope.showDesc = $attrs.showDesc || false;
            }
        }
    }]);

}(window.ecamp.ngApp));
