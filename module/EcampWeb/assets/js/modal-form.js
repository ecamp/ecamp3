/**
 * Created by pirmin on 26.10.14.
 */

(function(ngApp){

    ngApp.provider('$asyncModal', function(){
        var $asyncModalProvider = {
            options: {
                cache: true,
                windowTemplateUrl: '/web-assets/tpl/async-modal-window.html',
                windowClass: 'async-modal',
                controller: function(){}
            },
            $get: ['$modal', '$templateCache', function($modal, $templateCache){
                var $asyncModal = {};
                $asyncModal.open = function(modalOptions){
                    modalOptions = angular.extend({}, $asyncModalProvider.options, modalOptions);

                    if(!modalOptions.cache && modalOptions.templateUrl){
                        $templateCache.remove(modalOptions.templateUrl);
                    }

                    return $modal.open(modalOptions);
                };
                return $asyncModal;
            }]
        };
        return $asyncModalProvider
    });

    ngApp.directive('asyncModalWindow', [
        '$window', '$compile', '$timeout',
        function($window, $compile, $timeout){
            return {
                restrict: 'A',
                scope: false,
                link: function($scope, $element, $attrs, $ctrl){
                    var $controllerScope = $scope.$parent;

                    InitForms();
                    FocusFirstElement();

                    function InitForms() {
                        $element.find('form').each(InitForm);
                    }

                    function InitForm(idx, form){
                        var $form = $(form);

                        $form.submit(function(e){
                            FormSubmit($form, e);
                        });
                    }

                    function FormSubmit($form, event){
                        event.preventDefault();

                        $.ajax({
                            type: $form.attr('method'),
                            url:  $form.attr('action'),
                            data: $form.serialize(),
                            global: false
                        })
                        .then(
                            function(data, statusText, response){
                                if(response.status == 200){
                                    var locationHeader = response.getResponseHeader('Location');

                                    if(locationHeader){
                                        $controllerScope.$close('Location: ' + locationHeader);
                                        $window.location = locationHeader;
                                    } else {
                                        SetWindowContent(response.responseText);
                                        FocusFirstElement();
                                    }

                                } else if(response.status == 204){
                                    $controllerScope.$close(response.responseText);
                                }
                            },
                            function(response){
                                if(response.status == 500){
                                    SetWindowContent(response.responseText);
                                    FocusErrorElement();
                                }
                            }
                        );
                    }

                    function SetWindowContent(content){
                        $element.empty();
                        $element.append($compile(content)($controllerScope));
                        $controllerScope.$digest();

                        InitForms();
                    }

                    function FocusErrorElement(){
                        var errorElement = $element.find('form .has-error .form-control');
                        if(errorElement) {
                            FocusElement(errorElement.first());
                        } else {
                            FocusFirstElement();
                        }
                    }

                    function FocusFirstElement(){
                        var firstElement = $element.find('form [type!=hidden].form-control');
                        if(firstElement){
                            FocusElement(firstElement.first());
                        }
                    }

                    function FocusElement(el){
                        $timeout(function(){
                            el.focus();
                        }, 300);
                    }
                }
            };
        }
    ]);


    ngApp.directive('asyncModal', ['$asyncModal', function($asyncModal){
        return {
            restrict: 'A',
            scope: true,
            link: function($scope, $element, $attrs, $ctrl) {
                if($attrs.href !== undefined){
                    var url = $attrs.href;

                    $element.click(function(event){
                        event.preventDefault();

                        $asyncModal.open({
                            templateUrl: url,
                            cache: false
                        });

                        return false;
                    })
                }
            }
        }
    }]);

}(window.ecamp.ngApp));