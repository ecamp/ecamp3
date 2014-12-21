/**
 * Created by pirmin on 08.12.14.
 */

(function(ngApp){

    var events = CNS('ecamp.events');

    ngApp.directive('groupMembership', [function(){
        return {
            restrict: 'E',
            scope: true,
            controller: ['$scope', '$asyncModal', 'halClient', function($scope, $asyncModal, halClient){

                function Init(group, user){
                    var url = URI.expand('/api/v0/groups/{groupId}/members/{userId}', {
                        groupId: group,
                        userId: user
                    });

                    LoadMembershipResource(url);
                }

                function LoadMembershipResource(url){
                    $scope.isLoading = true;

                    return halClient.$get(url)
                        .then(MembershipResourceLoaded);
                }

                function MembershipResourceLoaded(membership){
                    $scope.membership = membership;

                    membership.$get('user').then(UserResourceLoaded);
                    membership.$get('group').then(GroupResourceLoaded);

                    $scope.role = membership.role;
                    $scope.status = membership.status;
                    $scope.description = membership.description;
                    $scope.isLoading = false;
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

                function ShowQuestion(event, modalUrl, action, query){
                    event.stopPropagation();
                    event.preventDefault();

                    var mi = $asyncModal.open({
                        templateUrl: modalUrl,
                        cache: false
                    });

                    mi.result.then(function(){
                        ExecuteAction(event, action, query);
                    });
                }

                function ExecuteAction(event, action, query){
                    event.stopPropagation();
                    event.preventDefault();

                    if($scope.membership.$has(action)){
                        var origStatus = $scope.status;

                        var uri = new URI($scope.membership.$href(action));
                        uri.search(query);

                        LoadMembershipResource(uri.toString())
                            .then(function(){ TriggerEvents(origStatus, $scope.status); });
                    } else {
                        throw "Unknown action [" + action + "]";
                    }
                }

                function TriggerEvents(oldStatus, newStatus){
                    events.trigger('group-membership', [oldStatus]);
                    events.trigger('group-membership', [newStatus]);
                }

                $scope.isLoading = false;

                $scope.HasAction = HasAction;
                $scope.ShowQuestion = ShowQuestion;
                $scope.ExecuteAction = ExecuteAction;

                this.Init = Init;
            }],
            link: function($scope, $element, $attrs, $ctrl) {
                $element.css({ 'display': 'inline' });
                $ctrl.Init($attrs.group, $attrs.user);
            }
        }
    }]);

    ngApp.directive('groupMembershipDescription', [function(){
        return {
            restrict: 'E',
            template: '<div data-ng-bind="description" style="display: inline"></div>',
            scope: false,
            link: function($scope, $element, $attrs, $ctrl) {
                $element.css({ 'display': 'inline' });
            }
        }
    }]);

    ngApp.directive('groupMembershipOperation', [function(){
        return {
            restrict: 'E',
            templateUrl: '/web-assets/group/groupMembershipOperation.html',
            scope: false,
            link: function($scope, $element, $attrs, $ctrl) {
                $element.css({ 'display': 'inline' });
                $scope.size = $attrs.size || 'sm';
                $scope.spinner = $attrs.spinner || 'none';
            }
        }
    }]);

}(window.ecamp.ngApp));
