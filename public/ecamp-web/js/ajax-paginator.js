CNS('ecamp.paginator');

jQuery(function($){
    ecamp.paginator.init = function(container){
        var $container = $(container);
        var events = $(CNS('ecamp.events'));

        function loadPage(url){
            $container.find('.page-info').hide();
            $container.find('.spinner').show();

            var promis = $.when($.get(url));
            promis.then(function(data){
                $data = $(data);
                ecamp.paginator.init($data);
                ecamp.initJQueryPlugins($data);

                if( $container.find('tbody').is(':visible')){
                    $container.replaceWith($data);
                } else {
                    $container.replaceWith($data);
                    hideTBody();
                }

            });
            return promis;
        }

        function hideTBody(){
            $container.find('tbody').fadeOut();
        }

        function showTBody(){
            $container.find('tbody').fadeIn();
        }

        function reloadCurrentPage(){
            loadPage($($container.find('a.refresh-url')).attr('href'))
            .then(showTBody);
        }

        $container.on('click', 'a.nav-link', function(event){
            event.preventDefault();

            hideTBody();
            $.when(loadPage($(event.target).attr('href')))
             .then(showTBody);
        });

        $container.find('input.hide-paginator-event').each(function(idx, element){
            events.on($(element).val(), hideTBody);
        });

        $container.find('input.show-paginator-event').each(function(idx, element){
            //events.on($(element).val(), showTBody);
        });

        $container.find('input.refresh-paginator-event').each(function(idx, element){
            events.on($(element).val(), reloadCurrentPage);
        });
    };

    $('.paginatorContainer').each(function(idx, container){
        ecamp.paginator.init(container);
    });

});

