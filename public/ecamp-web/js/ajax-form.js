jQuery(function($) {
    $( document ).on('click', '#asyncform-container button[type="submit"]',  function(event) {
    	var $target   = $('#asyncform-container');
        var $form 	  = $target.find('form');
        var $redirect = $form.attr('data-redirect-after-success');
 
        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: $form.serialize(),
            
            // prevents global error handling, errors handled locally
            global: false,  
            
            statusCode: {
				200: function(data, statusText, request){
					var locationHeader = request.getResponseHeader('Location');
					
					if(locationHeader){
						window.location = locationHeader;
					} else if($redirect){
						window.location = $redirect;
					} else {
						$target.modal('hide');
					}
				},
				
			    500: function(data, status){
					$target.html(data.responseText);
					var errorElement = $target.find('form .has-error .form-control');
					if(errorElement){
						errorElement.first().focus();
					} else {
						$target.find('form [type!=hidden].form-control').first().focus();
					}
				}
			}
        });
 
        event.preventDefault();
    });
    
    
    $(document).on('show.bs.modal', '#asyncform-container', function (e) {
    	var modal = $(e.target).data('bs.modal');
    	var opt = modal.options;

    	if(opt.remote && !opt.remoteLoaded){
			e.preventDefault();
			
			/* Remove this with Bootstrap v 3.0.4 */
			opt.remoteLoaded = true;
			setTimeout($.proxy(modal.show, modal), 500);

			/* Enable this with Bootstrap v 3.0.4 */
			//$(document).one('loaded.bs.modal', '#asyncform-container', $.proxy(modal.show, modal));
    	}
    });
    
    $(document).on('shown.bs.modal', '#asyncform-container', function (e) {
    	$(e.target).find('form [type!=hidden].form-control').first().focus();
    });
    
    $(document).on('loaded.bs.modal', '#asyncform-container', function (e) {
    	$(e.target).data('bs.modal').options.remoteLoaded = true;
    });
    
    /* clear container after closing modal. prevents from caching the content */
    $(document).on('hidden.bs.modal', '#asyncform-container', function (e) {
        $(e.target).removeData('bs.modal').find('.modal-content').html('');
    });
    
    /* global error handling */
    /* used e.g. if initial load of form throws an error */
    $(document).ajaxError(function( event, xhr, settings, exception ) {
    	alert(xhr.responseText);
    	$('.modal').modal('hide');
	});
    
});