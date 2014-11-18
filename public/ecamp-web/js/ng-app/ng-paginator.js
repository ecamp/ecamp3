/**
 * Created by pirminmattmann on 02.08.14.
 */
(function(){
    var app = ecamp.ngApp;

    app.directive('paginationContainer', ['$compile', '$http', function($compile, $http){
        return {
            restrict: 'E',
            scope: {
                type: '@',
                url: '@'
            },
            controller: function($scope){},
            link: function(scope, element, attrs, ctrl){
                var $element = $(element);
                var $pageContainer = $element;
                var $search = $element.find('.search-query');

                if($element.find('page-container').length){
                    $pageContainer = $element.find('page-container');
                }

                scope.query = {};
                scope.currentPage = 1;
                scope.updateCounter = 0;


                function load(pageNr){
                    $element.find('.page-info').hide();
                    $element.find('.spinner').show();

                    if(scope.url){
                        var url = scope.url.replace(':page', pageNr);
                        return $http.get(url, { params: scope.query })
                                    .success(function(){ scope.currentPage = pageNr; })
                                    .success(loadPageOnSuccess)
                                    .error(loadPageOnError);
                    }

                    return null;
                }

                function loadPageOnSuccess(data){
                    $pageContainer.html(data);

                    if(scope.updateCounter > 0){
                        $pageContainer.find('.pagination-container-body').css({ 'opacity': 0 });
                    }

                    $.each($pageContainer.children(), function(idx, elem){
                        $compile(elem)(scope);
                    });
                }

                function loadPageOnError(data, status, headers, config){
                }


                function loadPage(pageNr){
                    var l = load(pageNr);
                    if(l){
                        beginUpdate();
                        l.finally(endUpdate);
                    }
                }

                function refreshPage(){
                    loadPage(scope.currentPage);
                }

                function beginUpdate(){
                    scope.updateCounter++;
                    $pageContainer.find('.pagination-container-body').animate({ 'opacity': 0 });
                }

                function endUpdate(){
                    scope.updateCounter--;

                    if(scope.updateCounter == 0){
                        $pageContainer.find('.pagination-container-body').animate({ 'opacity': 1 });
                    }
                }

                function setQuery(query){
                    scope.query = query;
                }


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

                function stopTimeout(){
                    clearTimeout(changeTimeoutMinFreq); changeTimeoutMinFreq = null;
                    clearTimeout(changeTimeoutMaxFreq); changeTimeoutMaxFreq = null;
                }

                function inputValueChanged(){
                    if(changeTimeoutMinFreq == null){
                        changeTimeoutMinFreq = setTimeout(updateQuery, 1000);
                    }

                    clearTimeout(changeTimeoutMaxFreq);
                    changeTimeoutMaxFreq = setTimeout(updateQuery, 200);
                }

                function updateQuery(){
                    stopTimeout();

                    var q = {};
                    inputs.each(function(idx, input){
                        var $input = $(input);
                        q[$input.attr('name')] = $input.val();
                    });

                    paginationContainer.setQuery(q);
                    paginationContainer.refreshPage();
                }

                inputs.each(function(idx, input){
                    var $input = $(input);
                    var oldValue = $input.val();

                    $input.keyup(function(){
                        if($input.val() != oldValue){
                            oldValue = $input.val();
                            inputValueChanged();
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
                    paginationContainer.loadPage(scope.paginationPageNr);
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
                    paginationContainer.refreshPage();
                });
            }
        };
    });

})();