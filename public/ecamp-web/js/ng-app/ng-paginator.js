/**
 * Created by pirminmattmann on 02.08.14.
 */
(function(){
    var app = ecamp.ngApp;

    app.directive('paginationContainer', ['$compile', '$http', function($compile, $http){
        return {
            restrict: 'E',
            scope: {
                refresh: '@',
                url: '@'
            },
            controller: function($scope){
                var $element = null;
                var $pageContainer = null;
                var $search = null;

                $scope.query = {};
                $scope.currentPage = 1;
                $scope.updateCounter = 0;

                function Init(element){
                    $element = element;
                    $pageContainer = $element;
                    $search = $element.find('.search-query');

                    if($element.find('page-container').length){
                        $pageContainer = $element.find('page-container');
                    }
                }


                function Load(pageNr){
                    $element.find('.page-info').hide();
                    $element.find('.spinner').show();

                    if($scope.url){
                        var url = $scope.url.replace(':page', pageNr);
                        return $http.get(url, { params: $scope.query })
                            .success(function(){ $scope.currentPage = pageNr; })
                            .success(LoadPageOnSuccess)
                            .error(LoadPageOnError);
                    }

                    return null;
                }

                function LoadPageOnSuccess(data){
                    $pageContainer.html(data);

                    $.each($pageContainer.children(), function(idx, elem){
                        $compile(elem)($scope);
                    });
                }

                function LoadPageOnError(data, status, headers, config){
                }

                function LoadPage(pageNr){
                    Load(pageNr);
                    /*
                    var l = Load(pageNr);
                    if(l){
                        beginUpdate();
                        l.finally(endUpdate);
                    }
                    */
                }

                function RefreshPage(){
                    LoadPage($scope.currentPage);
                }

                function SetQuery(query){
                    $scope.query = query;
                }


                if($scope.refresh){
                    var events = CNS('ecamp.events');

                    var splits = $scope.refresh.split('::');

                    if(splits.length == 1){
                        events.on(splits[0], RefreshPage);
                    }
                    if(splits.length == 2){
                        events.on(splits[0], function(event, param){
                            if(param == splits[1]){ RefreshPage(); }
                        });
                    }
                }

                this.Init = Init;
                this.SetQuery = $scope.SetQuery = SetQuery;
                this.LoadPage = $scope.LoadPage = LoadPage;
                this.RefreshPage = $scope.RefreshPage = RefreshPage;
            },
            link: function(scope, element, attrs, ctrl){
                ctrl.Init(element);

/*
                if(scope.type){
                    var events = CNS('ecamp.events');
                    var contents = scope.type.split(' ');

                    $.each(contents, function(idx, content){
                        events.on(content + '.refresh', refreshPage);
                        events.on(content + '.beginUpdate', beginUpdate);
                        events.on(content + '.endUpdate', endUpdate);
                    });
                }

                scope.setQuery = ctrl.setQuery = setQuery;
                scope.loadPage = ctrl.loadPage = loadPage;
                scope.refreshPage = ctrl.refreshPage = refreshPage;
*/
            }
        }
    }]);

    app.directive('paginationHeader', function(){
        return {
            require: '^paginationContainer',
            restrict: 'E',
            transclude: true,
            template: '<div class="row"><div class="col-xs-12 ng-transclude"></div></div>',
            link: function(scope, element, attrs, paginationContainer){

                var inputs = element.find('input');
                var changeTimeoutMinFreq = null;
                var changeTimeoutMaxFreq = null;

                function StopTimeout(){
                    clearTimeout(changeTimeoutMinFreq); changeTimeoutMinFreq = null;
                    clearTimeout(changeTimeoutMaxFreq); changeTimeoutMaxFreq = null;
                }

                function InputValueChanged(){
                    if(changeTimeoutMinFreq == null){
                        changeTimeoutMinFreq = setTimeout(UpdateQuery, 1000);
                    }

                    clearTimeout(changeTimeoutMaxFreq);
                    changeTimeoutMaxFreq = setTimeout(UpdateQuery, 200);
                }

                function UpdateQuery(){
                    StopTimeout();

                    var q = {};
                    inputs.each(function(idx, input){
                        var $input = $(input);
                        q[$input.attr('name')] = $input.val();
                    });

                    paginationContainer.SetQuery(q);
                    paginationContainer.RefreshPage();
                }

                inputs.each(function(idx, input){
                    var $input = $(input);
                    var oldValue = $input.val();

                    $input.keyup(function(){
                        if($input.val() != oldValue){
                            oldValue = $input.val();
                            InputValueChanged();
                        }
                    });
                });

                inputs.first().focus();
            }
        }
    });

    app.directive('pageContainer', function(){
        return {
            require: '^paginationContainer',
            restrict: 'E',
            scope: false,
            link: function(){}
        };
    });

    app.directive('paginationPageNr', function(){
        return {
            require: '^paginationContainer',
            restrict: 'A',
            scope: {
                paginationPageNr: '@'
            },
            link: function(scope, element, attrs, paginationContainer) {
                $(element).click(function(){
                    paginationContainer.LoadPage(scope.paginationPageNr);
                });
            }
        };
    });

    app.directive('paginationPageRefresh', function(){
        return {
            require: '^paginationContainer',
            restrict: 'A',
            scope: {},
            link: function(scope, element, attrs, paginationContainer) {
                $(element).click(function(){
                    paginationContainer.RefreshPage();
                });
            }
        };
    });

})();