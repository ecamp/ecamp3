/**
 * Created by pirminmattmann on 03.08.14.
 */

(function($, jqUtil){

    jqUtil.tooltip = function(element){
        $(element).find('[data-toggle="tooltip"]').tooltip();
    };

    jqUtil.popover = function(element){
        $(element).find('[data-toggle="popover"]').popover();
    };

    jqUtil.selectpicker = function(element){
        $(element).find('.selectpicker').selectpicker();
    };

    jqUtil.autosize = function(element){
        $(element).find('textarea.autosize').autosize();
    };


    $(document).on('loaded.bs.modal', function(event){
        jqUtil.tooltip(event.target);
        jqUtil.popover(event.target);
        jqUtil.selectpicker(event.target);
        jqUtil.autosize(event.target);
    });

})(jQuery, CNS('eamp.jqUtil'));