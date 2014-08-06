/* ====================================================================
 * ==================================================================== */

/*jshint forin:true, noarg:true, noempty:true, eqeqeq:true, bitwise:true,
 strict:true, undef:true, unused:true, curly:true, browser:true, jquery:true,
 indent:4, maxerr:50 */

(function ($) {
    'use strict';

    function getLocation(data){
        return $(data).find('a.location').attr('href');
    }
    function getHtmlBody(data){
        return $(data).find('.html').html();
    }
    function getFragments(data, selector){
        var hash = {};
        $(data).find(selector).each(function(idx, elm){
            hash[elm.attr('name')] = $(elm).html();
        });
        return hash;
    }

    var Handlers = function () {};

    Handlers.prototype.redirect = function(e, $el, data) {
        var location = getLocation(data);
        if(location){
            window.location.href = location;
            return false;
        }
    };
    Handlers.prototype.replace = function(e, $el, data) {
        $($el.data('replace')).replaceWith(getHtmlBody(data.responseText));
    };
    Handlers.prototype.replaceClosest = function(e, $el, data) {
        $el.closest($el.data('replace-closest')).replaceWith(getHtmlBody(data.responseText));
    };
    Handlers.prototype.replaceInner = function(e, $el, data) {
        $($el.data('replace-inner')).html(getHtmlBody(data.responseText));
    };
    Handlers.prototype.replaceClosestInner = function(e, $el, data) {
        $el.closest($el.data('replace-closest-inner')).html(getHtmlBody(data.responseText));
    };
    Handlers.prototype.append = function(e, $el, data) {
        $($el.data('append')).append(getHtmlBody(data.responseText));
    };
    Handlers.prototype.prepend = function(e, $el, data) {
        $($el.data('prepend')).prepend(getHtmlBody(data.responseText));
    };
    Handlers.prototype.refresh = function(e, $el) {
        $.each($($el.data('refresh')), function(index, value) {
            $.get($(value).data('refresh-url'), function(data) {
                $(value).replaceWith(getHtmlBody(data));
            });
        });
    };
    Handlers.prototype.refreshClosest = function(e, $el) {
        $.each($($el.data('refresh-closest')), function(index, value) {
            $.get($(value).data('refresh-url'), function(data) {
                $el.closest($(value)).replaceWith(getHtmlBody(data));
            });
        });
    };
    Handlers.prototype.clear = function(e, $el) {
        $($el.data('clear')).html('');
    };
    Handlers.prototype.remove = function(e, $el) {
        $($el.data('remove')).remove();
    };
    Handlers.prototype.clearClosest = function(e, $el) {
        $el.closest($el.data('clear-closest')).html('');
    };
    Handlers.prototype.removeClosest = function(e, $el) {
        $el.closest($el.data('remove-closest')).remove();
    };
    Handlers.prototype.fragments = function(e, $el, data) {
        $.each(getFragments(data, '.fragments'), function(i, s){
            $(i).replaceWith(s);
        });
        $.each(getFragments(data, '.inner-fragments'), function(i, s){
            $(i).html(s);
        });
        $.each(getFragments(data, '.append-fragments'), function(i, s){
            $(i).append(s);
        });
        $.each(getFragments(data, '.prepend-fragments'), function(i, s){
            $(i).prepend(s);
        });
    };

    $(function () {
        $(document).on('eldarion-ajax:success', Handlers.prototype.redirect);
        $(document).on('eldarion-ajax:success', Handlers.prototype.fragments);
        $(document).on('eldarion-ajax:success', '[data-replace]', Handlers.prototype.replace);
        $(document).on('eldarion-ajax:success', '[data-replace-closest]', Handlers.prototype.replaceClosest);
        $(document).on('eldarion-ajax:success', '[data-replace-inner]', Handlers.prototype.replaceInner);
        $(document).on('eldarion-ajax:success', '[data-replace-closest-inner]', Handlers.prototype.replaceClosestInner);
        $(document).on('eldarion-ajax:success', '[data-append]', Handlers.prototype.append);
        $(document).on('eldarion-ajax:success', '[data-prepend]', Handlers.prototype.prepend);
        $(document).on('eldarion-ajax:success', '[data-refresh]', Handlers.prototype.refresh);
        $(document).on('eldarion-ajax:success', '[data-refresh-closest]', Handlers.prototype.refreshClosest);
        $(document).on('eldarion-ajax:success', '[data-clear]', Handlers.prototype.clear);
        $(document).on('eldarion-ajax:success', '[data-remove]', Handlers.prototype.remove);
        $(document).on('eldarion-ajax:success', '[data-clear-closest]', Handlers.prototype.clearClosest);
        $(document).on('eldarion-ajax:success', '[data-remove-closest]', Handlers.prototype.removeClosest);
    });
}(window.jQuery));
