/**
 * Created by pirmin on 30.10.14.
 */

(function(ngApp){
    ngApp.factory('PicassoData', ['ecampApiV0', function(ecampApiV0){

        var SortedDictionary = ecamp.core.util.SortedDictionary;

        function PicassoConfig(cfg){
            var _timeRange = [420, 1680];
            var _isFullscreen = false;
            var _minDayWidth = 150;
            var _dayWidth = _minDayWidth;

            if(cfg){
                _timeRange = cfg.timeRange || _timeRange;
                _isFullscreen = cfg.fullscreen || _isFullscreen;
                _minDayWidth = cfg.minDayWidth || _minDayWidth;
                _dayWidth = _minDayWidth;
            }

            Object.defineProperty(this, 'dayStartMin', {
                get: function(){ return _timeRange[0]; },
                set: function(value){ _timeRange[0] = value; }
            });
            Object.defineProperty(this, 'dayEndMin', {
                get: function(){ return _timeRange[1]; },
                set: function(value){ _timeRange[1] = value; }
            });

            Object.defineProperty(this, 'dayStart', {
                get: function(){ return new Date(0, 0, 1, 0, this.dayStartMin, 0); }
            });
            Object.defineProperty(this, 'dayEnd', {
                get: function(){ return new Date(0, 0, 1, 0, this.dayEndMin, 0); }
            });

            Object.defineProperty(this, 'timeRange', {
                get: function(){ return _timeRange; },
                set: function(value){ _timeRange = value; }
            });

            Object.defineProperty(this, 'isFullscreen', {
                get: function(){ return _isFullscreen; },
                set: function(value){ _isFullscreen = value; }
            });

            Object.defineProperty(this, 'minDayWidth', {
                get: function(){ return _minDayWidth; },
                set: function(value){
                    _minDayWidth = value;
                    _dayWidth = Math.max(_minDayWidth, _dayWidth);
                }
            });
            Object.defineProperty(this, 'dayWidth', {
                get: function(){ return _dayWidth; },
                set: function(value){ _dayWidth = Math.max(_minDayWidth, value); }
            });
        }

        function PicassoData(cfg){

            var _remoteData = null;
            var _config = new PicassoConfig(cfg);

            var _camp = null;
            var _periods = new SortedDictionary(PicassoPeriodSort);
            var _dates = new SortedDictionary(PicassoDateSort, PicassoDateInit);
            var _days = new SortedDictionary(PicassoDaySort, PicassoDayInit);
            var _eventCategories = new SortedDictionary();
            var _events = new SortedDictionary();
            var _eventInstances = new SortedDictionary(PicassoEventInstanceSort);


            var _periodAdapter = _periods.CreateAdapter(
                PicassoPeriodGetKey, PicassoPeriodCreate, PicassoPeriodUpdate);

            var _dateAdapter = _dates.CreateAdapter(
                PicassoDateGetKey, PicassoDateCreate, PicassoDateUpdate);

            var _dayAdapter = _days.CreateAdapter(
                PicassoDayGetKey, PicassoDayCreate, PicassoDayUpdate);

            var _eventCategoryAdapter = _eventCategories.CreateAdapter(
                PicassoEventCategoryGetKey, PicassoEventCategoryCreate, PicassoEventCategoryUpdate);

            var _eventAdapter = _events.CreateAdapter(
                PicassoEventKey, PicassoEventCreate, PicassoEventUpdate);

            var _eventInstanceAdapter = _eventInstances.CreateAdapter(
                PicassoEventInstanceGetKey, PicassoEventInstanceCreate, PicassoEventInstanceUpdate);



            Object.defineProperty(this, 'LoadCamp', { value: LoadCamp });
            Object.defineProperty(this, 'RefreshCamp', { value: RefreshEvents });
            Object.defineProperty(this, 'RefreshEvents', { value: RefreshEvents });
            Object.defineProperty(this, 'SaveEventInstance', { value: SaveEventInstance });

            Object.defineProperty(this, 'GetLength', { value: GetLength });
            Object.defineProperty(this, 'GetOffset', { value: GetOffset });
            Object.defineProperty(this, 'GetDayByLeft', { value: GetDayByLeft });
            Object.defineProperty(this, 'GetDayByTime', { value: GetDayByTime });
            Object.defineProperty(this, 'GetTime', { value: GetTime });

            Object.defineProperty(this, 'remoteData', { get: function(){ return _remoteData; } });


            Object.defineProperty(this, 'camp', { get: function(){ return _camp; } });
            Object.defineProperty(this, 'periods', { get: function(){ return _periods; } });
            Object.defineProperty(this, 'dates', { get: function(){ return _dates; } });
            Object.defineProperty(this, 'days', { get: function(){ return _days; } });
            Object.defineProperty(this, 'events', { get: function(){ return _events; } });
            Object.defineProperty(this, 'eventInstances', { get: function(){ return _eventInstances; } });


            Object.defineProperty(this, 'config', { get: function(){ return _config; } });
            Object.defineProperty(this, 'dayStartMin', { get: function(){ return _config.timeRange[0]; } });
            Object.defineProperty(this, 'dayEndMin', { get: function(){ return _config.timeRange[1]; } });

            Object.defineProperty(this, 'fullScreenClass', {
                get: function(){ return _config.isFullscreen ? 'picasso-fullscreen' : ''; }
            });

            Object.defineProperty(this, 'dayWidth', {
                get: function(){ return _config.dayWidth; },
                set: function(value){ _config.dayWidth = value; }
            });



            function LoadCamp(campId){
                _remoteData = null;

                if(campId){
                    _remoteData = new ecampApiV0(campId);
                    var promise = _remoteData.Update();
                    promise.then(RefreshCamp);
                    return promise;
                }
            }

            function RefreshCamp(){
                _camp = _remoteData ? _remoteData.GetCamp() : null;

                if (_camp) {
                    var periods = _remoteData.GetPeriods();
                    _periodAdapter(periods);

                    var periodModels = _periods.Values.filter(PicassoPeriodIsVisible);

                    var dates = [];
                    var days = [];

                    for(var idx = 0; idx < periodModels.length; idx++){
                        var periodId = periodModels[idx].id;
                        var periodDays = _remoteData.GetDays(function(d){
                            return d.periodId == periodId;
                        });

                        for(var jdx = 0; jdx < periodDays.length; jdx++){
                            days.push(periodDays[jdx]);
                            dates.push(periodDays[jdx].date);
                        }
                    }

                    _dayAdapter(days);
                    _dateAdapter(dates);

                } else {
                    days.Clear();
                    dates.Clear();
                }

                RefreshEvents();
            }

            function RefreshEvents(){
                if(_camp){
                    var eventCategories = _remoteData.GetEventCategories();
                    _eventCategoryAdapter(eventCategories);

                    var events = _remoteData.GetEvents();
                    _eventAdapter(events);


                    var periodModels = _periods.Values;
                    var eventInstances = [];

                    for(var idx = 0; idx < periodModels.length; idx++){
                        var periodId = periodModels[idx].id;
                        var periodEventInstances = _remoteData.GetEventInstances(function(ei){
                            return ei.periodId == periodId;
                        });

                        for(var jdx = 0; jdx < periodEventInstances.length; jdx++){
                            eventInstances.push(periodEventInstances[jdx]);
                        }
                    }
                    _eventInstanceAdapter(eventInstances);

                } else {
                    _eventInstances.Clear();
                }
            }

            function SaveEventInstance(eventInstanceModel){
                return _remoteData.SaveEventInstance(eventInstanceModel);

                /*
                var q = _remoteData.SaveEventInstance(eventInstanceModel);

                q.then(function(resp){

                    var key = PicassoEventInstanceGetKey(resp);
                    _eventInstances.Add(key, PicassoEventInstanceUpdate(eventInstanceModel, resp));
                });

                return q;
                */
            }


            /** @returns {number} */
            function GetLength(min){
                return min / (_config.timeRange[1] - _config.timeRange[0]);
            }

            /** @returns {number} */
            function GetOffset(min){
                return GetLength(min - _config.timeRange[0]);
            }

            /** @returns PicassoDay */
            function GetDayByLeft(absLeft){
                var leftOffset = Math.floor(absLeft / _config.dayWidth);
                var days = _days.Values;

                for(var idx = 0; idx < days.length; idx++){
                    if(days[idx].leftOffset == leftOffset){ return days[idx]; }
                }
                return null;
            }

            /** @returns PicassoDay */
            function GetDayByTime(periodId, time){
                var dayOffset = Math.floor(time / 1440);
                var days = _days.Values;

                for(var idx = 0; idx < days.length; idx++){
                    var day = days[idx];
                    if(day.periodId == periodId && day.dayOffset == dayOffset){
                        return day;
                    }
                }
                return null;
            }

            /** @returns {number} */
            function GetTime(absLeft, relTop){
                var day = GetDayByLeft(absLeft);
                var displayedDuration = _config.timeRange[1] - _config.timeRange[0];
                var dayTime = _config.timeRange[0] + relTop * displayedDuration;
                return 1440 * day.dayOffset + 15 * Math.round(dayTime / 15);
            }



            function PicassoPeriod(data){
                this.id = data.id;
                this.start = data.start;
                this.visible = true;

                this.getDays = function(){
                    var periodId = this.id;
                    return _days.Values.filter(function(dayModel){
                        return dayModel.periodId == periodId;
                    });
                }
            }
            function PicassoPeriodGetKey(data){
                return data.id;
            }
            function PicassoPeriodCreate(data){
                return new PicassoPeriod(data);
            }
            function PicassoPeriodUpdate(period, data){
                period.start = data.start;
                return period;
            }
            /** @returns {number} */
            function PicassoPeriodSort(p1, p2){
                return p1.start.getTime() - p2.start.getTime();
            }
            function PicassoPeriodIsVisible(p){
                return p.visible;
            }



            function PicassoDate(data){
                var d = new Date(data);
                this.id = d.getTime();
                this.date = d;
                this.leftOffset = 0;
                this.dayCount = 0;

                this.style = function(){
                    return {
                        left: this.leftOffset * _config.dayWidth,
                        width: this.dayCount * _config.dayWidth
                    };
                }
            }
            /** @returns {number} */
            function PicassoDateGetKey(data){
                var d = new Date(data);
                return d.getTime();
            }
            function PicassoDateCreate(data){
                return new PicassoDate(data);
            }
            function PicassoDateUpdate(picassoDate, data){
                picassoDate.date = new Date(data);
                return picassoDate;
            }
            /** @returns {number} */
            function PicassoDateSort(d1, d2){
                return d1.date.getTime() - d2.date.getTime();
            }
            function PicassoDateInit(date, idx, context){
                if(!context.leftOffset){
                    context.leftOffset = 0;
                }

                var dayCount = _dates.Count(function(day){
                    return day.date.getTime() == date.date.getTime();
                });

                date.dayCount = dayCount;
                date.leftOffset = context.leftOffset;
                context.leftOffset += dayCount;
            }



            function PicassoDay(data){
                this.id = data.id;
                this.periodId = data.periodId;
                this.dayOffset = data.offset;
                this.dayNr = data.offset + 1;
                this.date = new Date(data.date);
                this.leftOffset = 0;

                this.style = function(){
                    return {
                        left: this.leftOffset * _config.dayWidth,
                        width: _config.dayWidth
                    };
                }
            }
            function PicassoDayGetKey(data){
                return data.id;
            }
            function PicassoDayCreate(data){
                return new PicassoDay(data)
            }
            function PicassoDayUpdate(picassoDay, data){
                picassoDay.periodId = data.periodId;
                picassoDay.dayOffset = data.offset;
                picassoDay.dayNr = data.offset + 1;
                picassoDay.date = new Date(data.date);
                return picassoDay;
            }
            /** @returns {number} */
            function PicassoDaySort(d1, d2){
                var diff = d1.date.getTime() - d2.date.getTime();
                if(diff == 0){
                    if(d1 > d2) { diff =  1; }
                    if(d1 < d2) { diff = -1; }
                }
                return diff;
            }
            function PicassoDayInit(day, idx, context){
                if(!context.leftOffset){
                    context.leftOffset = 0;
                }
                day.leftOffset = context.leftOffset;
                context.leftOffset++;
            }



            function PicassoEventCategory(data){
                this.id = data.id;
                this.name = data.name;
                this.short = data.short;
                this.color = data.color;
                this.numbering = data.numbering;

                console.log(data);
            }
            function PicassoEventCategoryGetKey(data){
                return data.id;
            }
            function PicassoEventCategoryCreate(data){
                return new PicassoEventCategory(data);
            }
            function PicassoEventCategoryUpdate(picassoEventCategory, data){
                picassoEventCategory.name = data.name;
                picassoEventCategory.short = data.short;
                picassoEventCategory.color = data.color;
                picassoEventCategory.numbering = data.numbering;
                return picassoEventCategory;
            }



            function PicassoEvent(data){
                this.id = data.id;
                this.title = data.title;
                this.categoryId = data.categoryId;
                this.web = data.$href('web');

                Object.defineProperty(this, 'category', {
                    get: function(){ return _eventCategories.Get(this.categoryId); }.bind(this)
                });
            }
            function PicassoEventKey(data){
                return data.id;
            }
            function PicassoEventCreate(data){
                return new PicassoEvent(data);
            }
            function PicassoEventUpdate(picassoEvent, data){
                picassoEvent.title = data.title;
                picassoEvent.categoryId = data.categoryId;
                picassoEvent.web = data.$href('web');
                return picassoEvent;
            }



            function PicassoEventInstance(data){
                this.id  = data.id;
                this.periodId = data.periodId;
                this.start_min = data.start_min;
                this.end_min = data.end_min;
                this.left = data.left;
                this.width = data.width;
                this.eventId = data.eventId;

                Object.defineProperty(this, 'event', {
                    get: function(){ return _events.Get(this.eventId); }.bind(this)
                });


                /** @returns {string} */
                this.GetHash = function(){
                    return this.id + '$' + this.GetEventNr() + '::' +
                    '(' + this.start_min + '/' + _config.timeRange[0] + ') - ' +
                    '(' + this.end_min + '/' + _config.timeRange[1] + ') :: [' +
                    this.left + ', ' + this.width + '] :: ' +
                    _config.dayWidth;
                };

                this.GetDays = function(){
                    var periodId = this.periodId;
                    var start = this.start_min;
                    var end = this.end_min;

                    return _days.Values.filter(function(dayModel){
                        var dayStart = 1440 * dayModel.dayOffset + _config.timeRange[0];
                        var dayEnd = 1440 * dayModel.dayOffset + _config.timeRange[1];

                        return dayModel.periodId == periodId && dayStart < end && dayEnd > start;
                    });
                };

                /** @returns {number} */
                this.GetDayNr = function(){
                    return 1 + Math.floor(this.start_min / 1440);
                };

                /** @returns {*} */
                this.GetEventNr = function(){
                    var periodId = this.periodId;
                    var categoryId = this.event.categoryId;
                    var start = this.start_min;
                    var left = this.left;
                    var id = this.id;

                    var countEventInstances = _eventInstances.Count(function(eventInstanceModel){
                        if(eventInstanceModel.id == id){ return false; }
                        if(eventInstanceModel.periodId != periodId){ return false; }
                        if(eventInstanceModel.event.categoryId != categoryId){ return false; }
                        if(eventInstanceModel.start_min > start){ return false; }

                        if(Math.floor(eventInstanceModel.start_min / 1440) == Math.floor(start / 1440)){
                            if(eventInstanceModel.start_min < start){ return true; }
                            if(eventInstanceModel.left < left){ return true; }
                            if(eventInstanceModel.left == left) {
                                if (eventInstanceModel.id < id) { return true; }
                            }
                            return false;
                        }
                    });

                    var num = countEventInstances + 1;

                    switch (this.event.category.numbering) {
                        case '1':
                            return num;
                        case 'a':
                            return getAlphaNum(num).toLowerCase();
                        case 'A':
                            return getAlphaNum(num).toUpperCase();
                        case 'i':
                            return getRomanNum(num).toLowerCase();
                        case 'I':
                            return getRomanNum(num).toUpperCase();

                        default:
                            return num;
                    }
                }
            }
            /** @returns {string} */
            function PicassoEventInstanceGetKey(data){
                return data.id;
            }
            function PicassoEventInstanceCreate(data){
                return new PicassoEventInstance(data);
            }
            function PicassoEventInstanceUpdate(picassoEventInstance, data){
                picassoEventInstance.periodId = data.periodId;
                picassoEventInstance.start_min = data.start_min;
                picassoEventInstance.end_min = data.end_min;
                picassoEventInstance.left = data.left;
                picassoEventInstance.width = data.width;
                picassoEventInstance.eventId = data.eventId;
                return picassoEventInstance;
            }
            /** @returns {number} */
            function PicassoEventInstanceSort(ei1, ei2){
                if(ei1 > ei2) { return  1; }
                if(ei1 < ei2) { return -1; }
                return 0;
            }

        }



        function getAlphaNum(num){
            var alphaNum = '';
            num = num - 1;

            if(num >= 26){
                alphaNum = alphaNum + getAlphaNum(Math.floor(num / 26));
            }
            alphaNum = alphaNum + String.fromCharCode(65 + (num % 26));
            return alphaNum;
        }

        function getRomanNum(num){
            var map = {
                'M': 1000, 'CM': 900, 'D': 500, 'CD': 400,
                'C': 100, 'XC': 90, 'L': 50, 'XL': 40,
                'X': 10, 'IX': 9, 'V': 5, 'IV': 4, 'I': 1
            };
            var romanNum = '';

            while(num > 0){
                for(var rom in map){
                    if(num >= map[rom]){
                        num -= map[rom];
                        romanNum = romanNum + rom;
                        break;
                    }
                }
            }

            return romanNum;
        }

        return PicassoData;
    }]);

})(window.ecamp.ngApp);
