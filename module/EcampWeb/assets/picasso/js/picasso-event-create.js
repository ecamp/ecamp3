/**
 * Created by pirmin on 30.10.14.
 */

(function(ngApp) {
    ngApp.factory('PicassoEventCreate', ['$filter', '$timeout', '$asyncModal',
        function($filter, $timeout, $asyncModal){

        var $dateFilter = $filter('date');

        function PicassoEventCreate(picassoData, picassoElement){

            var _picassoData = picassoData;
            var _picassoElement = picassoElement;
            var _isCreating = false;
            var _visualHelpers = [];

            var _click1 = null;
            var _click2 = null;

            var _lastLeft = null;
            var _periodId = null;


            Object.defineProperty(this, 'isCreating', { get: function(){ return _isCreating; } });
            Object.defineProperty(this, 'helpers', { get: function(){ return _visualHelpers; } });

            Object.defineProperty(this, 'MouseDown', { value: MouseDown });
            Object.defineProperty(this, 'MouseMove', { value: MouseMove });
            Object.defineProperty(this, 'MouseUp', { value: MouseUp });

            function Reset(){
                _isCreating = false;
                _visualHelpers = [];
                _click1 = null;
                _click2 = null;
                _lastLeft = null;
                _periodId = null;
            }


            function MouseDown(event){
                Reset();

                if(_picassoElement.eventInstances.is(event.target)){
                    var offset = _picassoElement.eventInstances.offset();
                    var left = event.pageX - offset.left;
                    var top = event.pageY - offset.top;
                    var relTop = _picassoElement.CalcRelY(top);

                    _isCreating = true;

                    var day = _picassoData.GetDayByLeft(left);
                    _lastLeft = left;
                    _periodId = day.periodId;

                    var time = picassoData.GetTime(left, relTop);
                    _click1 = Math.round(time / 15) * 15;
                    _click2 = Math.round(time / 15) * 15;

                    CreateVisualHelpers();
                }
            }

            function MouseMove(event){
                if(_isCreating){
                    event.preventDefault();

                    var offset = _picassoElement.eventInstances.offset();
                    var left = event.pageX - offset.left;
                    var top = event.pageY - offset.top;
                    var relTop = _picassoElement.CalcRelY(top);

                    var day = _picassoData.GetDayByLeft(left);
                    if(day.periodId == _periodId){
                        _lastLeft = left;
                    }

                    var time = _picassoData.GetTime(_lastLeft, relTop);
                    _click2 = Math.round(time / 15) * 15;

                    CreateVisualHelpers();
                }
            }

            function MouseUp(event){
                if(_isCreating){
                    var start = Math.min(_click1, _click2);
                    var end = Math.max(_click1, _click2);

                    if(start != end) {
                        var startDay = _picassoData.GetDayByTime(_periodId, start);
                        var endDay = _picassoData.GetDayByTime(_periodId, end);

                        var startDate = new Date(0, 0, 1, 0, start, 0);
                        var endDate = new Date(0, 0, 1, 0, end, 0);

                        start -= 1440 * startDay.dayOffset;
                        end -= 1440 * endDay.dayOffset;

                        var startTime = $dateFilter(startDate, 'HH:mm');
                        var endTime = $dateFilter(endDate, 'HH:mm');

                        console.log('Create Event: ' + _periodId + ' / ' + start + ' - ' + end);


                        var url = '/web/group/PBS/Bundeslager/picasso/createEvent';
                        url += '?periodId=' + _periodId;
                        url += '&eventInstance[startday]=' + startDay.id;
                        url += '&eventInstance[endday]=' + endDay.id;
                        url += '&eventInstance[starttime]=' + startTime;
                        url += '&eventInstance[endtime]=' + endTime;

                        $asyncModal.open({
                            templateUrl: url
                        });
                    }
                }

                Reset();
            }


            function CreateVisualHelpers(){
                var helpers = [];

                var start = Math.min(_click1, _click2);
                var end = Math.max(_click1, _click2);

                var days = _picassoData.days.Values.filter(function(day){
                    var dayStart = 1440 * day.dayOffset + _picassoData.dayStartMin;
                    var dayEnd = 1440 * day.dayOffset + _picassoData.dayEndMin;

                    return day.periodId == _periodId && dayStart < end && dayEnd > start;
                });

                for(var idx = 0; idx < days.length; idx++){
                    var day = days[idx];

                    var dayStart = 1440 * day.dayOffset + _picassoData.dayStartMin;
                    var dayEnd = 1440 * day.dayOffset + _picassoData.dayEndMin;

                    var visualHelperStart = Math.max(start, dayStart) - 1440 * day.dayOffset;
                    var visualHelperEnd = Math.min(end, dayEnd) - 1440 * day.dayOffset;
                    var isStart = start >= dayStart;
                    var isEnd = end <= dayEnd;

                    helpers.push({
                        style: {
                            borderTop: isStart ? 'dashed black 2px' : 'none',
                            borderBottom: isEnd ? 'dashed black 2px' : 'none',
                            borderLeft: 'dashed black 2px',
                            borderRight: 'dashed black 2px',
                            borderTopLeftRadius: isStart ? 10 : 0,
                            borderTopRightRadius: isStart ? 10 : 0,
                            borderBottomLeftRadius: isEnd ? 10 : 0,
                            borderBottomRightRadius: isEnd ? 10 : 0,
                            backgroundColor: 'white',
                            opacity: 0.4,
                            position: 'absolute',
                            left: (day.leftOffset + 0.02) * _picassoData.dayWidth,
                            width: 0.96 * _picassoData.dayWidth,
                            top: (100 * _picassoData.GetOffset(visualHelperStart)) + '%',
                            height: (100 * _picassoData.GetLength(visualHelperEnd - visualHelperStart)) + '%',
                            textAlign: 'center'
                        },
                        start: new Date(0, 0, 1, 0, start, 0),
                        end: new Date(0, 0, 1, 0, end, 0)
                    })
                }

                _visualHelpers = helpers;
            }

        }

        return PicassoEventCreate;

    }]);

})(window.ecamp.ngApp);
