/**
 * Created by pirmin on 26.10.14.
 */

(function(ngApp){

    ngApp.provider('$asyncModal', function(){
        var $asyncModalProvider = {
            options: {
                windowTemplateUrl: '/web-assets/tpl/async-modal-window.html',
                controller: 'BootstrapModalController'
            },
            $get: ['$modal', function($modal){
                var $asyncModal = {};
                $asyncModal.open = function(modalOptions){
                    modalOptions = angular.extend({}, $asyncModalProvider.options, modalOptions);

                    return $modal.open(modalOptions);
                };
                return $asyncModal;
            }]
        };
        return $asyncModalProvider
    });


    ngApp.controller(
        'BootstrapModalController',
        ['$scope',
            function($scope){

                $scope.cancel = function(){
                    $scope.$dismiss('cancel');
                };
            }
        ]
    );

    ngApp.directive('asyncModalWindow', [
        '$compile', '$timeout',
        function($compile, $timeout){
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
                            global: false,

                            statusCode: {
                                200: function(data, statusText, response){
                                    var locationHeader = response.getResponseHeader('Location');

                                    if(locationHeader){
                                        window.location = locationHeader;
                                    } else {
                                        SetWindowContent(response.responseText);
                                        FocusFirstElement();
                                    }
                                },

                                204: function(/* data, statusText, response */){
                                    $controllerScope.$dismiss('cancel');
                                },

                                500: function(data /*, statusText, response */){
                                    SetWindowContent(data.responseText);
                                    FocusErrorElement();
                                }
                            }
                        });
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


/*
    $(document).on('loaded.bs.modal', '[role=dialog].modal', function(e){
        console.log('loaded.bs.modal');
        console.log(e);
    });

    $(document).on('shown.bs.modal', '[role=dialog].modal', function(e){
        console.log('shown.bs.modal');
        console.log(e);
    });

    $(document).on('hidden.bs.modal', '[role=dialog].modal', function(e){
        console.log('hidden.bs.modal');
        console.log(e);
    });
*/

}(window.ecamp.ngApp));