jQuery(function($) {
    $( document ).on('click', '#asyncform-container button[type="submit"]',  function(event) {
    	var $target   = $('#asyncform-container');
        var $content  = $target.find('.modal-content');
        var $form 	  = $target.find('form');

        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: $form.serialize(),
            
            // prevents global error handling, errors handled locally
            global: false,  
            
            statusCode: {

				200: function(data, statusText, response){
                    var locationHeader = response.getResponseHeader('Location');

                    if(locationHeader){
						window.location = locationHeader;
					} else {
                        $content.html(response.responseText);
                        ecamp.initJQueryPlugins($content);
					}
				},

                204: function(/* data, statusText, response */){
                    $target.modal('hide');
                },

                500: function(data /*, statusText, response */){
                    $content.html(data.responseText);
					var errorElement = $content.find('form .has-error .form-control');
					if(errorElement){
						errorElement.first().focus();
					} else {
                        $content.find('form [type!=hidden].form-control').first().focus();
					}
				}
			}
        });
 
        event.preventDefault();
    });
    
    $(document).on('shown.bs.modal', '#asyncform-container', function (e) {
        $(e.target).find('form [type!=hidden].form-control').first().focus();
    });

    $(document).on('loaded.bs.modal', '#asyncform-container', function(event){
        ecamp.initJQueryPlugins(event.target);
    });
    
    /* clear container after closing modal. prevents from caching the content */
    $(document).on('hidden.bs.modal', '#asyncform-container', function (e) {
        $(e.target).removeData('bs.modal').find('.modal-content').html('');
    });
    
    /* global error handling */
    /* used e.g. if initial load of form throws an error */
    $(document).ajaxError(function(event, xhr /*, settings, exception */) {
    	alert(xhr.responseText);
    	$('.modal').modal('hide');
	});
    
});