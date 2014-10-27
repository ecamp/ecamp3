/**
 * Created by pirmin on 17.08.14.
 */

(function(ngApp){
    var SortedDictionary = ecamp.core.util.SortedDictionary;

    function PicassoController($scope, $timeout, $filter){
        $scope.log = function(){ console.log.apply(console, arguments) };

        var $element = null;
        var $content = null;

        this.GetElement = function(){ return $element; };
        this.GetContent = function(){ return $content; };
        this.Scope = function(){ return $scope; };


        $scope.remoteData = {
            camps: {},
            periods: {},
            days: {},
            events: {},
            eventInstances: [
                new ecamp.entity.EventInstance(12345, 0, 12, 1980, 2400),
                new ecamp.entity.EventInstance(12346, 0, 13, 1200, 2010),
                new ecamp.entity.EventInstance(12347, 0, 13, 600, 900)
            ],

            PeriodsByCamp: function(campId){
                if(campId == 1) {
                    return [
                        new ecamp.entity.Period(12, 1, new Date(2014, 6, 16), 'PeriodDesc 12', '#aaf'),
                        new ecamp.entity.Period(13, 1, new Date(2014, 6, 17), 'PeriodDesc 13', '#faa')
                    ];
                }
                return [];
            },
            DaysByPeriod: function(periodId){
                if(periodId == 12){
                    return [
                        new ecamp.entity.Day(1234, 12, 0, new Date(2014, 6, 16)),
                        new ecamp.entity.Day(1235, 12, 1, new Date(2014, 6, 17)),
                        new ecamp.entity.Day(1236, 12, 2, new Date(2014, 6, 18)),
                        new ecamp.entity.Day(1237, 12, 3, new Date(2014, 6, 19)),
                        new ecamp.entity.Day(1238, 12, 4, new Date(2014, 6, 20)),
                        new ecamp.entity.Day(1239, 12, 5, new Date(2014, 6, 21))
                    ];
                }
                if(periodId == 13){
                    return [
                        new ecamp.entity.Day(1334, 13, 0, new Date(2014, 6, 17)),
                        new ecamp.entity.Day(1335, 13, 1, new Date(2014, 6, 18)),
                        new ecamp.entity.Day(1336, 13, 2, new Date(2014, 6, 19))
                    ];
                }
                return [];
            },


            EventInstancesByPeriod: function(periodId){
                var eventInstances = $scope.remoteData.eventInstances;

                return eventInstances.filter(function(ei){
                    return ei.periodId == periodId;
                });
            }

            /*
            EventInstancesByDay: function(dayId){
                if(dayId == 1235){
                    return [
                        $scope.remoteData.eventInstances[0]
                    ];
                }
                if(dayId == 1334) {
                    return [
                        $scope.remoteData.eventInstances[1],
                        $scope.remoteData.eventInstances[2]
                    ];
                }
                if(dayId == 1335){
                    return [
                        $scope.remoteData.eventInstances[1]
                    ];
                }
                return [];
            }
            */
        };

        $scope.config = {
            timeRange: [420, 1440],
            fullScreen: false
        };

        $scope.appearance = {
            fullScreenClass: '',
            dayWidth: 150
        };

        $scope.timeline = null;


        // Camp:
        $scope.camp = null;

        // Periods:
        $scope.periods = new SortedDictionary(
            ecamp.picasso.entity.Period.Sort
        );

        // Dates:
        $scope.dates = new SortedDictionary(
            ecamp.picasso.entity.Date.Sort,
            function(date, idx, context){
                if(!context.leftOffset){
                    context.leftOffset = 0;
                }

                var dayCount = $scope.days.Count(function(day){
                    return day.date.getTime() == date.date.getTime();
                });

                date.dayCount = dayCount;
                date.leftOffset = context.leftOffset;
                context.leftOffset += dayCount;
            }
        );

        // Days:
        $scope.days = new SortedDictionary(
            ecamp.picasso.entity.Day.Sort,
            function(day, idx, context){
                if(!context.leftOffset){
                    context.leftOffset = 0;
                }
                day.leftOffset = context.leftOffset;
                context.leftOffset++;
            }
        );

        // EventInstances:
        $scope.eventInstances = new SortedDictionary(
            ecamp.picasso.entity.EventInstance.Sort
        );


        var periodAdapter = $scope.periods.CreateAdapter(
            ecamp.picasso.entity.Period.Key,
            ecamp.picasso.entity.Period.Insert,
            ecamp.picasso.entity.Period.Update
        );

        var dateAdapter = $scope.dates.CreateAdapter(
            ecamp.picasso.entity.Date.Key,
            function(d){ return ecamp.picasso.entity.Date.Insert($scope, d); },
            ecamp.picasso.entity.Date.Update
        );

        var dayAdapter = $scope.days.CreateAdapter(
            ecamp.picasso.entity.Day.Key,
            function(d){ return ecamp.picasso.entity.Day.Insert($scope, d); },
            ecamp.picasso.entity.Day.Update
        );

        var eventInstanceAdapter = $scope.eventInstances.CreateAdapter(
            ecamp.picasso.entity.EventInstance.Key,
            function(ei){ return ecamp.picasso.entity.EventInstance.Insert($scope, ei); },
            ecamp.picasso.entity.EventInstance.Update
        );

        $scope.$watch('config.timeRange', RefreshPicasso);
        $scope.$watch('config.fullScreen', RefreshFullScreen);

        $(window).resize(ThrottleScopeApplys(function(){
            RefreshTimeline();
            RefreshAppearance();
        }, 200));


        function Init(element){
            console.log(element);

            $element = element.find('.picasso-body');
            $content = element.find('.event-instances');

            InitEventCreateHelper();
            RefreshPicasso();
        }
        this.Init = Init;



        function RefreshFullScreen(){
            if($element == null) return;

            // Refresh FullScreen:
            $scope.appearance.fullScreenClass = $scope.config.fullScreen ? 'picasso-fullscreen' : '';

            $timeout(function(){
                RefreshTimeline();
                RefreshAppearance();
            });
        }

        function RefreshPicasso(){
            if($element == null) return;

            RefreshTimeline();
            RefreshData();
            RefreshAppearance();
        }

        function RefreshAppearance(){
            if($element == null) return;

            // Refresh FullScreen:
            $scope.appearance.fullScreenClass = $scope.config.fullScreen ? 'picasso-fullscreen' : '';

            // Refresh DayWidth:
            var dayCount = Math.max(1, $scope.days.Count());
            $scope.appearance.dayWidth = Math.max(150, $element.width() / dayCount);
        }

        function RefreshTimeline(){
            if($element == null) return;

            var height = $content.height();
            var startMin = $scope.config.timeRange[0];
            var endMin = $scope.config.timeRange[1];

            if( $scope.timeline == null
            ||  $scope.timeline.height != height
            ||  $scope.timeline.startMin != startMin || $scope.timeline.endMin != endMin
            ) {
                // TimeRange changed - RefreshTimeRange:
                $scope.timeline = CreateTimeline(startMin, endMin);
            }
        }

        function RefreshData(){
            if($element == null) return;

            $scope.camp = new ecamp.entity.Camp(1, 'CampName', 'CampTitle', 'CampMotto');

            RefreshCamp();
            RefreshEvents();
        }

        function RefreshCamp(){
            if($scope.camp){
                var periods = $scope.remoteData.PeriodsByCamp($scope.camp.id);

                periodAdapter(periods);

                var periodModels = $scope.periods.Values.filter(ecamp.picasso.entity.Period.IsVisible);

                var dates = [];
                var days = [];

                for(var idx = 0; idx < periodModels.length; idx++){
                    var periodId = periodModels[idx].id;
                    var periodDays = $scope.remoteData.DaysByPeriod(periodId);

                    for(var jdx = 0; jdx < periodDays.length; jdx++){
                        days.push(periodDays[jdx]);
                        dates.push(periodDays[jdx].date)
                    }
                }

                dayAdapter(days);
                dateAdapter(dates);

            } else {
                $scope.periods.Clear();
                $scope.dates.Clear();
                $scope.days.Clear();
            }
        }

        this.RefreshEvents = function(){ $timeout(RefreshEvents); };
        function RefreshEvents() {
            if($scope.camp) {
                var periodModels = $scope.periods.Values;
                var eventInstances = [];

                for(var idx = 0; idx < periodModels.length; idx++){
                    var periodModel = periodModels[idx];
                    var evs = $scope.remoteData.EventInstancesByPeriod(periodModel.id);

                    for(var jdx = 0; jdx < evs.length; jdx++){
                        eventInstances.push(evs[jdx]);
                    }
                }
                eventInstanceAdapter(eventInstances);

            } else {
                $scope.eventInstances.Clear();
            }

            console.log($scope.eventInstances);
        }

        function LinkEventInstance(eventInstanceScope, eventInstanceElement){
            var eventInstanceModel = eventInstanceScope.data;
            var isDragging = false;
            eventInstanceScope.hover = false;
            eventInstanceScope.eventViews = new SortedDictionary();

            var eventInstanceAdapter = eventInstanceScope.eventViews.CreateAdapter(
                function(eventView){ return eventView.day.id + eventView.eventInstance.id; },
                function(eventView){ return eventView; },
                function(eventViewModel, eventView){ return eventViewModel; }
            );

            eventInstanceScope.$watch('data.GetHash()', UpdateEventViews);

            function UpdateEventViews() {
                var days = eventInstanceModel.GetDays();
                var firstDay = days[0];
                var eventViews = [];

                for (var idx = 0; idx < days.length; idx++) {

                    var eventView = {};
                    eventView.day = days[idx];
                    eventView.firstDay = firstDay;
                    eventView.eventInstance = eventInstanceModel;

                    eventView.border = function(){
                        var dayMinOffset = 1440 * this.day.dayOffset;
                        var dayStart = dayMinOffset + $scope.timeline.startMin;
                        var dayEnd = dayMinOffset + $scope.timeline.endMin;
                        var isStart = (eventInstanceModel.start >= dayStart);
                        var isEnd = (eventInstanceModel.end <= dayEnd);

                        return {
                            borderLeft: "solid black 1px",
                            borderRight: "solid black 1px",
                            borderTop: isStart ? "solid black 1px" : "none",
                            borderBottom: isEnd ? "solid black 1px" : "none",

                            borderTopLeftRadius: isStart ? 10 : 0,
                            borderTopRightRadius: isStart ? 10 : 0,
                            borderBottomLeftRadius: isEnd ? 10 : 0,
                            borderBottomRightRadius: isEnd ? 10 : 0,
                            pointerEvents: 'none',
                            zIndex: eventInstanceScope.hover ? 10001 : null

                        }
                    };

                    eventView.style = function(){
                        var dayMinOffset = 1440 * this.day.dayOffset;
                        var dayStart = dayMinOffset + $scope.timeline.startMin;
                        var dayEnd = dayMinOffset + $scope.timeline.endMin;

                        var isStart = (eventInstanceModel.start >= dayStart);
                        var isEnd = (eventInstanceModel.end <= dayEnd);
                        var start = Math.max(eventInstanceModel.start, dayStart);
                        var end = Math.min(eventInstanceModel.end, dayEnd);

                        return {
                            //transition: 'left 1s, top 1s, height 1s, width 1s',
                            left: (eventInstanceModel.left + this.day.leftOffset) * $scope.appearance.dayWidth,
                            width: eventInstanceModel.width * $scope.appearance.dayWidth,
                            top: GetOffset(start - dayMinOffset) + '%',
                            height: GetLength(end - start) + '%',

                            zIndex: eventInstanceScope.hover ?
                                10000 : 100 * firstDay.dayOffset + eventInstanceModel.GetEventNr(),

                            isStart: isStart,
                            isEnd: isEnd
                        };

                    };

                    eventView.bgStyle = function(){
                        var dayMinOffset = 1440 * this.day.dayOffset;
                        var dayStart = dayMinOffset + $scope.timeline.startMin;
                        var dayEnd = dayMinOffset + $scope.timeline.endMin;

                        var isStart = (eventInstanceModel.start >= dayStart);
                        var isEnd = (eventInstanceModel.end <= dayEnd);

                        return {
                            left: 0,
                            right: 0,
                            top: 0,
                            bottom: 0,
                            borderTopLeftRadius: isStart ? 10 : 0,
                            borderTopRightRadius: isStart ? 10 : 0,
                            borderBottomLeftRadius: isEnd ? 10 : 0,
                            borderBottomRightRadius: isEnd ? 10 : 0,

                            backgroundColor: '#298ae8',
                            opacity: eventInstanceScope.hover ? 0.9 : 0.6
                        };
                    };
                    eventView.mouseover = function(){
                        eventInstanceScope.hover = true;

                    };
                    eventView.mouseleave = function(){
                        eventInstanceScope.hover = false;
                    };

                    eventViews.push(eventView);

                }

                //eventInstanceScope.eventViews = eventViews;
                eventInstanceAdapter(eventViews);

                if(!isDragging) {
                    $timeout(InitInteractable);
                }
            }

            function InitInteractable(){
                var divs = eventInstanceElement.find('.event-instance');

                var duration = 0;
                var deltaTime = 0;

                divs.draggable({
                    //cursorAt: { left: 0 },

                    helper: function(event){
                        var target = $(event.target);
                        console.log(target);

                        var h = $('<div />');
                        //var h = $('<div style="background-color: red" >Z</div>');
                        h.css({
                            'height': target.height(),
                            'width': target.width()
                        });
                        return h;
                    },

                    start: function(event, ui){
                        isDragging = true;

                        duration = eventInstanceModel.end - eventInstanceModel.start;

                        var dayModel = GetDay(ui.position.left + ui.helper.width() / 2);
                        deltaTime = GetTime(dayModel, ui.position.top) - eventInstanceModel.start;
                    },
                    drag: function(event, ui){
                        console.log(ui);

                        var dayStart = $scope.timeline.startMin;
                        var dayEnd = $scope.timeline.endMin;
                        var deltaTop = 15 * $scope.timeline.height / (dayEnd - dayStart);
                        var deltaLeft = $scope.appearance.dayWidth / 10;

                        ui.position.top = Math.round(ui.position.top / deltaTop) * deltaTop;
                        ui.position.left = Math.round(ui.position.left / deltaLeft) * deltaLeft;

                        var dayModel = GetDay(ui.position.left + ui.helper.width() / 2);
                        var start = GetTime(dayModel, ui.position.top) - deltaTime;
                        start = Math.max(start, 0);

                        var inDayLeft = ui.position.left - dayModel.leftOffset * $scope.appearance.dayWidth;
                        inDayLeft = inDayLeft / $scope.appearance.dayWidth;
                        inDayLeft = Math.max(inDayLeft, 0);
                        inDayLeft = Math.min(inDayLeft, 1 - eventInstanceModel.width);
                        inDayLeft = Math.round(10 * inDayLeft) / 10;


                        eventInstanceModel.periodId = dayModel.periodId;
                        eventInstanceModel.start = start;
                        eventInstanceModel.end = start + duration;
                        eventInstanceModel.left = inDayLeft;

                        console.log(eventInstanceModel);

                        eventInstanceScope.$apply(UpdateEventViews());
                    },
                    stop: function(){
                        isDragging = false;
                        eventInstanceScope.$apply(UpdateEventViews());

                        console.log(
                            'Save: EventInstance[ID=' + eventInstanceModel.id + '] { ' +
                            'periodId: ' + eventInstanceModel.periodId + ', ' +
                            'start: ' + eventInstanceModel.start + ', ' +
                            'end: ' + eventInstanceModel.end + ', ' +
                            'left: ' + eventInstanceModel.left +
                            '}');
                    }
                });

                for(var idx = 0; idx < divs.length; idx++){
                    var div = $(divs[idx]);

                    var divPosition = null;
                    var periodId = null;
                    var lastDay = null;
                    var doLength = false;
                    var doWidth = false;

                    div.find('.resize-se, .resize-s, .resize-e').draggable({
                        cursorAt: { left: 0, top: 0 },
                        helper: function(){ return $('<div />'); },

                        start: function(event, ui){
                            isDragging = true;
                            var target = $(event.target);

                            doLength = target.is('.resize-s, .resize-se');
                            doWidth = target.is('.resize-e, .resize-se');

                            divPosition = div.position();
                            periodId = GetDay(divPosition.left).periodId;
                        },

                        drag: function(event, ui){
                            var top = divPosition.top + ui.position.top + ui.helper.height() / 2;
                            var left = divPosition.left + ui.position.left + ui.helper.width() / 2;


                            if(lastDay == null){
                                lastDay = GetDay(divPosition.left);
                            }

                            if(doLength){
                                var dayModel = GetDay(left);
                                if(dayModel.periodId == periodId) {
                                    lastDay = dayModel;
                                }

                                var end = GetTime(lastDay, top);
                                eventInstanceModel.end = Math.max(end, eventInstanceModel.start + 15);
                            }

                            if(doWidth){
                                var width = left - (lastDay.leftOffset + eventInstanceModel.left) * $scope.appearance.dayWidth;
                                width = width / $scope.appearance.dayWidth;
                                width = Math.max(width, 0.3);
                                width = Math.min(width, 1 - eventInstanceModel.left);
                                width = Math.round(width * 10) / 10;

                                eventInstanceModel.width = width;
                            }

                            if(doLength || doWidth) {
                                eventInstanceScope.$digest();
                            }
                        },

                        stop: function(){
                            isDragging = false;

                            doLength = false;
                            doWidth = false;

                            eventInstanceScope.$apply(UpdateEventViews());

                            console.log(
                                'Save: EventInstance[ID=' + eventInstanceModel.id + '] { ' +
                                'end: ' + eventInstanceModel.end + ', ' +
                                'width: ' + eventInstanceModel.width +
                                '}');
                        }
                    });
                }
            }

        }
        this.LinkEventInstance = LinkEventInstance;


        function GetTime(dayModel, top){

            var time = $scope.timeline.startMin + ($scope.timeline.endMin - $scope.timeline.startMin) * top / $scope.timeline.height;
            time = Math.round(time / 15) * 15;

            time += 1440 * dayModel.dayOffset;

            return time;
        }

        function GetDay(left){
            var leftOffset = Math.floor(left / $scope.appearance.dayWidth);
            var days = $scope.days.Values;

            for(var idx = 0; idx < days.length; idx++){
                if(days[idx].leftOffset == leftOffset){
                    return days[idx];
                }
            }
        }


        function InitEventCreateHelper(){
            $scope.createEvent = {
                isCreating: false,
                helpers: []
            };

            function Reset() {
                $scope.createEvent.helpers = [];
                $scope.createEvent.isCreating = false;
            }

            function CreateHelpers(){
                var helpers = [];
                var periodId = $scope.createEvent.periodId;
                var start = Math.min($scope.createEvent.click1, $scope.createEvent.click2);
                var end = Math.max($scope.createEvent.click1, $scope.createEvent.click2);

                var days = $scope.days.Values.filter(function(dayModel){
                    var dayStart = 1440 * dayModel.dayOffset + $scope.timeline.startMin;
                    var dayEnd = 1440 * dayModel.dayOffset + $scope.timeline.endMin;

                    return dayModel.periodId == periodId && dayStart < end && dayEnd > start;
                });

                for(var idx = 0; idx < days.length; idx++){
                    var day = days[idx];

                    var dayStart = 1440 * day.dayOffset + $scope.timeline.startMin;
                    var dayEnd = 1440 * day.dayOffset + $scope.timeline.endMin;

                    var helperStart = Math.max(start, dayStart) - 1440 * day.dayOffset;
                    var helperEnd = Math.min(end, dayEnd) - 1440 * day.dayOffset;
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
                            left: (day.leftOffset + 0.02) * $scope.appearance.dayWidth,
                            width: 0.96 * $scope.appearance.dayWidth,
                            top: GetOffset(helperStart) + '%',
                            height: GetLength(helperEnd - helperStart) + '%',
                            textAlign: 'center'
                        },
                        start: new Date(0, 0, 1, 0, start, 0),
                        end: new Date(0, 0, 1, 0, end, 0)
                    });
                }

                $scope.createEvent.helpers = helpers;
                $scope.$digest();
            }

            $content.mousedown(function(event){
                event.preventDefault();
                Reset();

                if($content.is(event.target)){
                    var offset = $content.offset();
                    var top = event.pageY - offset.top;
                    var left = event.pageX - offset.left;

                    $scope.createEvent.isCreating = true;

                    var day = GetDay(left);
                    $scope.createEvent.day = day;
                    $scope.createEvent.lastDay = day;
                    $scope.createEvent.periodId = day.periodId;

                    var time = GetTime(day, top);
                    $scope.createEvent.click1 = Math.round(time / 15) * 15;
                    $scope.createEvent.click2 = Math.round(time / 15) * 15;

                    CreateHelpers();
                }
            });

            $content.mousemove(function(event){
                if($scope.createEvent.isCreating){
                    event.preventDefault();

                    var offset = $content.offset();
                    var top = event.pageY - offset.top;
                    var left = event.pageX - offset.left;

                    var day = GetDay(left);
                    if(day.periodId == $scope.createEvent.periodId){
                        $scope.createEvent.lastDay = day;
                    }

                    var time = GetTime($scope.createEvent.lastDay, top);
                    $scope.createEvent.click2 = Math.round(time / 15) * 15;

                    CreateHelpers();
                }
            });

            $content.mouseup(function(){
                if($scope.createEvent.isCreating) {
                    event.preventDefault();

                    var periodId = $scope.createEvent.periodId;
                    var start = Math.min($scope.createEvent.click1, $scope.createEvent.click2);
                    var end = Math.max($scope.createEvent.click1, $scope.createEvent.click2);

                    console.log('Create Event: ' + periodId + ' / ' + start + ' - ' + end);
                }

                Reset();
                $scope.$digest();
            });
        }


        /*
         * CreateTimeline
         * Erstellt die Timeline links und rechts vom Picasso.
         */
        function CreateTimeline(startMin, endMin){
            var timeline = {
                height: $content.height(),
                startMin: startMin,
                endMin: endMin,
                start: new Date(0, 0, 1, 0, startMin, 0),
                end: new Date(0, 0, 1, 0, endMin, 0),
                durationMin: endMin - startMin,
                slots: []
            };

            var start = 60 * Math.floor(timeline.startMin / 60);
            var end = 60 * Math.ceil(timeline.endMin / 60);

            for (var min = start; min < end; min += 60){
                var slotStart = Math.max(startMin, min);
                var slotEnd = Math.min(endMin, min + 60);
                var slotDuration = slotEnd - slotStart;
                var oddEvenClass = (Math.floor(min / 60) % 2 == 1) ? 'odd' : 'even';
                var fontSize = GetFontSize(slotDuration);

                timeline.slots.push({
                    style: {
                        top: GetOffset(slotStart) + '%',
                        height: GetLength(slotDuration) + '%',
                        fontSize: fontSize
                    },
                    class: 'picasso-timeline-slot-' + oddEvenClass,
                    start: new Date(0, 0, 1, 0, slotStart, 0),
                    end: new Date(0, 0, 1, 0, slotEnd, 0),
                    showTime: !!(fontSize)
                });
            }
            return timeline;
        }

        function GetLength(min){
            return 100 * min / ($scope.config.timeRange[1] - $scope.config.timeRange[0]);
        }

        function GetOffset(min){
            return GetLength(min - $scope.config.timeRange[0]);
        }

        function GetFontSize(min){
            var height = GetLength(min) * $content.height() / 100;

            var fontSize = height - 6;
            fontSize = (fontSize <= 14) ? fontSize : 14;
            fontSize = (fontSize >=  8) ? fontSize : 0;

            return fontSize > 0 ? (fontSize + 'px') : null;
        }

        function ThrottleScopeApplys(fn, delay){
            var idx = Number.NaN;
            return function(){
                if(idx) clearTimeout(idx);
                idx = setTimeout(function(){
                    idx = Number.NaN;
                    $scope.$apply(fn);
                }, delay);
            };
        }

        function SortId(id1, id2){
            if(id1 > id2){
                return 1
            }
            if(id1 < id2) {
                return -1
            }
            return 0;
        }
    }




    ngApp.directive('picasso', [function(){
        return {
            restrict: 'E',
            templateUrl: '/web-assets/picasso/tpl/picasso.html',
            scope: {},
            controller: PicassoController,

            link: function($scope, $element, $attrs, $ctrl){
                $ctrl.Init($element);
            }
        };
    }]);

    ngApp.directive('eventInstance', [function(){
        return {
            restrict: 'E',
            require: '^picasso',
            templateUrl: '/web-assets/picasso/tpl/event-instance.html',
            scope: {
                data: "="
            },

            link: function($scope, $element, $attrs, $ctrl){
                $ctrl.LinkEventInstance($scope, $element);

                console.log('EventInstance linked: ' + $scope.data.id);
            }
        }
    }]);

    ngApp.directive('animateStyle', [function(){
        return {
            restrict: 'A',
            link: function($scope, $element, $attrs, $ctrl){
                $scope.$watch($attrs.animateStyle, function(newStyles, oldStyles){
                    /*
                    if (oldStyles && (newStyles !== oldStyles)) {
                        var keys = Object.keys(oldStyles);
                        for(var idx = 0; idx < keys.length; idx++){
                            $element.css(keys[idx], '');
                        }
                    }
                    */
                    if (newStyles) $element.animate(newStyles, 500);
                }, true);
            }
        }
    }]);

}(window.ecamp.ngApp));