(function() {

    function EcampApiV0Resource(halClient, listHref, itemHref){

        this.$get = function(id, options){
            return halClient.$get(itemHref.replace('{id}', id), options);
        };

        this.$getList = function(options){
            return halClient.$get(listHref, options);
        };

        this.$post = function(options, data){
            return halClient.$post(listHref, options, data);
        };

        this.$put = function(options, data){
            return halClient.$put(href, options, data);
        };

        this.$patch = function(options, data){
            return halClient.$patch(href, options, data);
        };

        this.$del = function(options){
            return halClient.$del(href, options);
        };
    }

    var module = angular.module('ecamp.api.v0', [
        'angular-hal'
    ]);

    module.service('ApiV0.ResqueJobs', ['halClient', function (halClient){
        return new EcampApiV0Resource(halClient, '/api/v0/resque/jobs', '/api/v0/resque/jobs/{id}');
    }]);

    module.service('ApiV0.ResqueWorkers', ['halClient', function(halClient){
        return new EcampApiV0Resource(halClient, '/api/v0/resque/workers', '/api/v0/resque/workers/{id}');
    }]);

    module.service('ApiV0.Camps', ['halClient', function(halClient){
        return new EcampApiV0Resource(halClient, '/api/v0/camps', '/api/v0/camps/{id}');
    }]);

})();