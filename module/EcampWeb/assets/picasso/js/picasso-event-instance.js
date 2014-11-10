/**
 * Created by pirmin on 04.11.14.
 */

(function(ngApp){
    ngApp.factory('PicassoEventInstance', ['$timeout', function($timeout) {

        var SortedDictionary = ecamp.core.util.SortedDictionary;

        function PicassoEventInstance(picassoData, picassoElement) {

            var _picassoData = picassoData;
            var _picassoElement = picassoElement;
            var _userEventIsProcessing = false;

            Object.defineProperty(this, 'Link', { value: Link });

            function Link(scope, element){

                var eventInstanceModel = scope.data;
                var eventViews = [];

                scope.hover = false;
                Object.defineProperty(scope, 'eventViews', {
                    get: function(){ return eventViews; }
                });

                scope.$watch('data.GetHash()', UpdateEventViews);


                function UpdateEventViews() {
                    var days = eventInstanceModel.GetDays();

                    var firstDay = days[0];
                    eventViews = [];

                    for (var idx = 0; idx < days.length; idx++) {

                        var id = eventInstanceModel.id + '.' + idx;
                        var day = days[idx];
                        var eventView = new EventView(id, day, firstDay, eventInstanceModel);

                        eventViews.push(eventView);
                    }

                    $timeout(InitInteractable);
                }


                function InitInteractable(){
                    InitDraggable();
                    InitResizable();
                }

                function InitDraggable(){
                    var eventInstances = element.find('.event-instance');

                    var duration = 0;
                    var deltaTime = 0;

                    eventInstances.draggable({
                        helper: function(event){
                            var target = $(event.target);

                            var h = $('<div />');
                            h.css({ 'height': target.height(), 'width': target.width() });
                            return h;
                        },
                        start: function(event, ui){
                            _userEventIsProcessing = true;

                            duration = eventInstanceModel.end_min - eventInstanceModel.start_min;

                            var relTop = _picassoElement.CalcRelY(ui.position.top);
                            var left = ui.position.left + ui.helper.width() / 2;
                            deltaTime = _picassoData.GetTime(left, relTop) - eventInstanceModel.start_min;
                        },
                        drag: function(event, ui){
                            var dayStart = _picassoData.dayStartMin;
                            var dayEnd = _picassoData.dayEndMin;
                            var deltaTop = 15 * _picassoElement.eventInstanceHeight / (dayEnd - dayStart);
                            var deltaLeft = _picassoData.dayWidth / 10;

                            ui.position.top = Math.round(ui.position.top / deltaTop) * deltaTop;
                            ui.position.left = Math.round(ui.position.left / deltaLeft) * deltaLeft;

                            var relTop = _picassoElement.CalcRelY(ui.position.top);
                            var left = ui.position.left + ui.helper.width() / 2;
                            var dayModel = _picassoData.GetDayByLeft(left);
                            var periodModel = _picassoData.periods.Get(dayModel.periodId);
                            var start = _picassoData.GetTime(left, relTop) - deltaTime;
                            start = Math.max(start, 0);
                            start = Math.min(start, 24 * 60 * periodModel.getDays().length - duration);

                            var inDayLeft = ui.position.left - dayModel.leftOffset * _picassoData.dayWidth;
                            inDayLeft = inDayLeft / _picassoData.dayWidth;
                            inDayLeft = Math.max(inDayLeft, 0);
                            inDayLeft = Math.min(inDayLeft, 1 - eventInstanceModel.width);
                            inDayLeft = Math.round(10 * inDayLeft) / 10;


                            eventInstanceModel.periodId = dayModel.periodId;
                            eventInstanceModel.start_min = start;
                            eventInstanceModel.end_min = start + duration;
                            eventInstanceModel.left = inDayLeft;

                            scope.$apply(UpdateEventViews());
                        },
                        stop: function(){
                            _userEventIsProcessing = false;
                            scope.$apply(UpdateEventViews());

                            SaveEventInstance(eventInstanceModel);

                            console.log(
                                'Save: EventInstance[ID=' + eventInstanceModel.id + '] { ' +
                                'periodId: ' + eventInstanceModel.periodId + ', ' +
                                'start: ' + eventInstanceModel.start_min + ', ' +
                                'end: ' + eventInstanceModel.end_min + ', ' +
                                'left: ' + eventInstanceModel.left +
                                '}');

                        }
                    });
                }

                function InitResizable(){
                    var eventInstanceResizables = element.find(
                        '.event-instance .resize-se, ' +
                        '.event-instance .resize-s, ' +
                        '.event-instance .resize-e'
                    );

                    var divPosition = null;
                    var periodId = null;
                    var lastLeft = null;
                    var doLength = false;
                    var doWidth = false;


                    eventInstanceResizables.draggable({
                        cursorAt: { left: 0, top: 0 },
                        helper: function(){ return $('<div />'); },

                        start: function(event, ui){
                            _userEventIsProcessing = true;
                            var target = $(event.target);

                            doLength = target.is('.resize-s, .resize-se');
                            doWidth = target.is('.resize-e, .resize-se');

                            divPosition = target.closest('.event-instance').position();
                            periodId = _picassoData.GetDayByLeft(divPosition.left).periodId;
                        },
                        drag: function(event, ui){
                            var top = divPosition.top + ui.position.top + 5;
                            var left = divPosition.left + ui.position.left + 5;
                            var relTop = _picassoElement.CalcRelY(top);

                            if(lastLeft == null){
                                lastLeft = left;
                            }

                            if(doLength){
                                if(_picassoData.GetDayByLeft(left).periodId == periodId) {
                                    lastLeft = left;
                                }

                                var end = _picassoData.GetTime(lastLeft, relTop);
                                eventInstanceModel.end_min = Math.max(end, eventInstanceModel.start_min + 15);
                            }

                            if(doWidth){
                                var day = _picassoData.GetDayByLeft(lastLeft);
                                var width = left - (day.leftOffset + eventInstanceModel.left) * _picassoData.dayWidth;
                                width = width / _picassoData.dayWidth;
                                width = Math.max(width, 0.3);
                                width = Math.min(width, 1 - eventInstanceModel.left);
                                width = Math.round(width * 10) / 10;

                                eventInstanceModel.width = width;
                            }

                            if(doLength || doWidth) {
                                scope.$digest();
                            }
                        },
                        stop: function(){
                            _userEventIsProcessing = false;

                            doLength = false;
                            doWidth = false;

                            scope.$apply(UpdateEventViews());
                            SaveEventInstance(eventInstanceModel);

                            console.log(
                                'Save: EventInstance[ID=' + eventInstanceModel.id + '] { ' +
                                'end: ' + eventInstanceModel.end_min + ', ' +
                                'width: ' + eventInstanceModel.width +
                                '}');
                        }
                    });
                }


                function EventView(id, day, firstDay, eventInstance){
                    this.id = id;
                    this.day = day;
                    this.firstDay = firstDay;
                    this.eventInstance = eventInstance;

                    Object.defineProperty(this, 'Border', { value: Border });
                    Object.defineProperty(this, 'Style', { value: Style });
                    Object.defineProperty(this, 'BgStyle', { value: BgStyle });

                    Object.defineProperty(this, 'IsStart', { value: IsStart });
                    Object.defineProperty(this, 'IsEnd', { value: IsEnd });

                    Object.defineProperty(this, 'Mouseover', { value: Mouseover });
                    Object.defineProperty(this, 'Mouseleave', { value: Mouseleave });
                    Object.defineProperty(this, 'Class', { value: Class });


                    function Border(){
                        var isStart = this.IsStart();
                        var isEnd = this.IsEnd();

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
                            zIndex: scope.hover ? 10001 : null
                        }
                    }

                    function Style(){
                        var dayMinOffset = 1440 * this.day.dayOffset;
                        var dayStart = dayMinOffset + _picassoData.dayStartMin;
                        var dayEnd = dayMinOffset + _picassoData.dayEndMin;

                        var start = Math.max(eventInstanceModel.start_min, dayStart);
                        var end = Math.min(eventInstanceModel.end_min, dayEnd);

                        return {
                            //transition: _userEventIsProcessing ? '' : /* 'top 1s, height 1s', */ 'left 60s, top 60s, height 60s, width 60s',
                            position: 'absolute',
                            left: (eventInstanceModel.left + this.day.leftOffset) * _picassoData.dayWidth,
                            width: eventInstanceModel.width * _picassoData.dayWidth,
                            top: (100 * _picassoData.GetOffset(start - dayMinOffset)) + '%',
                            height: (100 * _picassoData.GetLength(end - start)) + '%',

                            zIndex: scope.hover ?
                                10000 : 100 * firstDay.dayOffset + eventInstanceModel.GetEventNr()
                        };

                    }

                    function BgStyle(){
                        var isStart = this.IsStart();
                        var isEnd = this.IsEnd();

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
                            opacity: scope.hover ? 0.9 : 0.6
                        };
                    }


                    function IsStart(){
                        var dayMinOffset = 1440 * this.day.dayOffset;
                        var dayStart = dayMinOffset + _picassoData.dayStartMin;

                        return (eventInstanceModel.start_min >= dayStart);
                    }
                    function IsEnd(){
                        var dayMinOffset = 1440 * this.day.dayOffset;
                        var dayEnd = dayMinOffset + _picassoData.dayEndMin;

                        return (eventInstanceModel.end_min <= dayEnd);
                    }

                    function Mouseover(){
                        scope.hover = true;
                    }
                    function Mouseleave(){
                        scope.hover = false;
                    }

                    function Class(){
                        return _userEventIsProcessing ? 'user-event' : '';
                    }
                }


                function SaveEventInstance(eventInstanceModel){
                    picassoData.SaveEventInstance(eventInstanceModel)
                }
            }
        }

        return PicassoEventInstance;

    }]);

})(window.ecamp.ngApp);
