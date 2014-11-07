/**
 * Created by pirmin on 21.10.14.
 */

(function(ngApp){

    ngApp.factory('ecampApiV0', ['$q', 'halClient', function($q, halClient){

        function CampData(campId){
            var _campId = campId;
            var _data = {
                camp: null,
                periods: {},
                days: {},
                events: {},
                eventInstances: {}
            };

            this.Update = function(fn){
                var q = $q.defer();
                var doResolve = q.resolve.bind(q, this);

                halClient.$get('/api/v0/camps/' + _campId)
                    .then(function(camp){
                        _data.camp = camp;
                        LoadPeriods(camp)
                            .then(doResolve)
                            .catch(q.reject);
                    })
                    .catch(q.reject);

                if(fn){ q.promise.then(fn); }
                return q.promise;
            };

            function LoadPeriods(camp){
                _data.periods = {};

                console.log(camp);

                var q = $q.defer();
                camp.$get('periods')
                    .then(function(list){
                        list.$get('items')
                            .then(function(periods){

                                for(var idx = 0; idx < periods.length; idx++){
                                    var id = periods[idx].id;
                                    _data.periods[id] = periods[idx];
                                }

                                $q.all([
                                    LoadDays(periods),
                                    LoadEventInstances(periods)
                                ]).then(q.resolve).catch(q.reject);
                            })
                            .catch(q.reject);
                    })
                    .catch(q.reject);
                return q.promise;
            }

            function LoadDays(periods){
                _data.days = {};

                var qs = periods.map(function(period){
                    var q = $q.defer();

                    period.$get('days')
                        .then(function(list){
                            list.$get('items')
                                .then(function(days){

                                    for(var idx = 0; idx < days.length; idx++){
                                        var id = days[idx].id;
                                        _data.days[id] = days[idx];
                                    }
                                    q.resolve();
                                })
                                .catch(q.reject);
                        })
                        .catch(q.reject);

                    return q.promise;
                });

                return $q.all(qs);
            }

            function LoadEventInstances(periods){
                _data.eventInstances = {};

                var qs = periods.map(function(period){
                    var q = $q.defer();

                    period.$get('event_instances')
                        .then(function(list){
                            list.$get('items')
                                .then(function(eventInstances){

                                    for(var idx = 0; idx < eventInstances.length; idx++){
                                        var id = eventInstances[idx].id;
                                        _data.eventInstances[id] = eventInstances[idx];
                                    }

                                    q.resolve();
                                })
                                .catch(q.reject);
                        })
                        .catch(q.reject);

                    return q.promise;
                });

                return $q.all(qs);
            }




            this.GetData = function(){
                return _data;
            };


            this.GetCamp = function(){
                return _data.camp;
            };


            this.GetPeriod = function(id){
                return _data.periods[id];
            };
            this.GetPeriods = function(fn){
                var periods = Object.keys(_data.periods).map(this.GetPeriod);
                if(fn){ periods = periods.filter(fn); }
                return periods;
            };


            this.GetDay = function(id){
                return _data.days[id];
            };
            this.GetDays = function(fn){
                var days = Object.keys(_data.days).map(this.GetDay);
                if(fn){ days = days.filter(fn); }
                return days;
            };


            this.GetEvent = function(id){
                return _data.events[id];
            };
            this.GetEvents = function(fn){
                var events = Object.keys(_data.events).map(this.GetEvent);
                if(fn){ events = events.filter(fn); }
                return events;
            };


            this.GetEventInstance = function(id){
                return _data.eventInstances[id];
            };
            this.GetEventInstances = function(fn){
                var eventInstances = Object.keys(_data.eventInstances).map(this.GetEventInstance);
                if(fn){ eventInstances = eventInstances.filter(fn); }
                return eventInstances;
            };
            this.UpdateEventInstance = function(eventInstance){
                var id = eventInstance.id;
                _data.eventInstances[id] = eventInstance;

                console.log(eventInstance);
                console.log(_data.eventInstances[id]);
            };
            this.SaveEventInstance = function(eventInstanceModel){
                var eventInstance = this.GetEventInstance(eventInstanceModel.id);

                return eventInstance
                    .$put('self', null, eventInstanceModel)
                    .then(this.UpdateEventInstance);
            }
        }

        return CampData;

    }]);

})(window.ecamp.ngApp);