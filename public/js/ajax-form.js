jQuery(function($) {
    $( document ).on('submit', 'form[data-async]',  function(event) {
        var $form = $(this);
        var $target   = $($form.attr('data-target'));
        var $redirect = $form.attr('data-redirect-after-success');
 
        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: $form.serialize(),
            
            // prevents global error handling, errors handled locally
            global: false,  
            error: function(xhr, status) {
                $target.html(xhr.responseText);
            },
            
            success: function(data, status) {
            	if( $redirect ){
            		window.location = $redirect;
            	}
            	else{
            		$target.modal('hide');
            	}
            }
        });
 
        event.preventDefault();
    });
    
    /* clear container after closing modal. prevents from caching the content */
    $(document).on('hidden.bs.modal', '#asyncform-container', function (e) {
        $(e.target).removeData('bs.modal').html('');
    });
    
    /* global error handling */
    /* used e.g. if initial load of form throws an error */
    $(document).ajaxError(function( event, xhr, settings, exception ) {
    	alert(xhr.responseText);
    	$('.modal').modal('hide');
	});
    
});