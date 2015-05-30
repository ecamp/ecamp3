
(function(ngApp) {
    ngApp.config(function ($translateProvider) {

        $translateProvider.translations('de', {
            URL_CAMP_CREATE_EVENT: '/web/de/camp/{campId}/picasso/createEvent',
            URL_CAMP_UPDATE_EVENT_INSTANCE: '/web/de/camp/{campId}/picasso/updateEventInstance'
        });

        $translateProvider.preferredLanguage('de');
    });

}(window.ecamp.ngApp));