/**
 * Created by pirminmattmann on 03.08.14.
 */

(function($, ngUtil){

    function getInjector(){
        return angular.element(document).injector();
    }

    ngUtil.compile = function(element){
        getInjector().invoke(function($compile){
            var scope = angular.element(element).scope();
            $compile(element)(scope);
        });
    };


    $(document).on('loaded.bs.modal', function(event){
        ngUtil.compile(event.target);
    });

})(jQuery, CNS('ecamp.ngUtil'));