/**
 * Created by pirmin on 15.12.14.
 */

(function(){

    var events = CNS('ecamp.events');

    var module = angular.module('ecamp-collaboration', [
        'ui.bootstrap',
        'angular-hal'
    ]);

    module.directive('campCollaboration', [function(){
        return {
            restrict: 'E',
            scope: true,
            controller: ['$scope', '$asyncModal', 'halClient', function($scope, $asyncModal, halClient){

                function Init(camp, user){
                    var url = URI.expand('/api/v0/camps/{campId}/collaborators/{userId}', {
                        campId: camp,
                        userId: user
                    });

                    LoadCollaborationResource(url);
                }

                function LoadCollaborationResource(url){
                    $scope.isLoading = true;

                    return halClient.$get(url)
                        .then(CollaborationResourceLoaded);
                }

                function CollaborationResourceLoaded(collaboration){
                    $scope.collaboration = collaboration;

                    collaboration.$get('user').then(UserResourceLoaded);
                    collaboration.$get('camp').then(CampResourceLoaded);

                    $scope.role = collaboration.role;
                    $scope.status = collaboration.status;
                    $scope.description = collaboration.description;
                    $scope.isLoading = false;
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

                    if($scope.collaboration.$has(action)){
                        var origStatus = $scope.status;

                        var uri = new URI($scope.collaboration.$href(action));
                        uri.search(query);

                        LoadCollaborationResource(uri.toString())
                            .then(function(){ TriggerEvents(origStatus, $scope.status); });
                    } else {
                        throw "Unknown action [" + action + "]";
                    }
                }

                function TriggerEvents(oldStatus, newStatus){
                    events.trigger('camp-collaboration', [oldStatus]);
                    events.trigger('camp-collaboration', [newStatus]);
                }

                $scope.isLoading = false;

                $scope.HasAction = HasAction;
                $scope.ShowQuestion = ShowQuestion;
                $scope.ExecuteAction = ExecuteAction;

                this.Init = Init;
            }],
            link: function($scope, $element, $attrs, $ctrl) {
                $element.css({ 'display': 'inline' });
                $ctrl.Init($attrs.camp, $attrs.user);
            }
        }
    }]);

    module.directive('campCollaborationDescription', [function(){
        return {
            restrict: 'E',
            template: '<div data-ng-bind="description" style="display: inline"></div>',
            scope: false,
            link: function($scope, $element, $attrs, $ctrl) {
                $element.css({ 'display': 'inline' });
            }
        }
    }]);

    module.directive('campCollaborationOperation', [function(){
        return {
            restrict: 'E',
            templateUrl: '/web-assets/tpl/camp/collaboration/operation.html',
            scope: false,
            link: function($scope, $element, $attrs, $ctrl) {
                $element.css({ 'display': 'inline' });
                $scope.size = $attrs.size || 'sm';
                $scope.spinner = $attrs.spinner || 'none';
            }
        }
    }]);

})();
