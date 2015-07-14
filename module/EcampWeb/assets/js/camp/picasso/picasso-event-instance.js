/**
 * Created by pirmin on 04.11.14.
 */

(function(){

    var module = angular.module('ecamp.picasso.event-instance', [
        'ecamp.SortedDictionary'
    ]);

    module.factory('PicassoEventInstance', [
        '$timeout', '$window', 'SortedDictionary',
        function($timeout, $window, SortedDictionary) {

            function PicassoEventInstance(picassoData, picassoElement) {

                var _eventHover = {};
                var _picassoData = picassoData;
                var _picassoElement = picassoElement;

                Object.defineProperty(this, 'Link', { value: Link });

                function Link(scope, element){

                    var eventInstanceModel = scope.data;
                    var eventViews = new SortedDictionary();
                    var eventViewsAdapter = eventViews.CreateAdapter(
                        function(data){ return data.id; },
                        function(data){ return new EventView(data.id, data.day, data.eventInstance, data.isHandle); },
                        function(eventView, data){
                            eventView.day = data.day;
                            eventView.eventInstance = data.eventInstance;
                            eventView.isHandle = data.isHandle;

                            return eventView;
                        }
                    );

                    Object.defineProperty(scope, 'hover', {
                        get: function(){ return !!_eventHover[eventInstanceModel.eventId] || scope.userEventIsProcessing; },
                        set: function(val){ _eventHover[eventInstanceModel.eventId] = val; }
                    });

                    var _userEventProcessing = 0;

                    function BeginUserEvent(){
                        _userEventProcessing++;
                        picassoData.config.beginUserEvent();
                    }

                    function EndUserEvent(){
                        _userEventProcessing--;
                        picassoData.config.endUserEvent();
                    }

                    Object.defineProperty(scope, 'userEventIsProcessing', {
                        get: function(){ return _userEventProcessing > 0; }
                    });

                    Object.defineProperty(scope, 'eventViews', {
                        get: function(){ return eventViews.Values; }
                    });

                    scope.$watch('data.GetHash()', UpdateEventViews);


                    function UpdateEventViews() {
                        var data = [{
                            'id': eventInstanceModel.id,
                            'day': eventInstanceModel.GetFirstDay(),
                            'eventInstance': eventInstanceModel,
                            'isHandle': true
                        }];
                        var days = eventInstanceModel.GetDays();

                        for (var idx = 0; idx < days.length; idx++) {
                            var id = eventInstanceModel.id + '.' + idx;
                            var day = days[idx];
                            data.push({
                                'id': id,
                                'day': day,
                                'eventInstance': eventInstanceModel,
                                'isHandle': false
                            });
                        }
                        eventViewsAdapter(data);

                        $timeout(InitInteractable);
                    }


                    function InitInteractable(){
                        InitDraggable();
                        InitResizable();
                    }

                    var draggable_info = {
                        duration: 0,
                        deltaTime: 0
                    };

                    function InitDraggable(){

                        var eventInstances = element.find('.event-instance');
                        var firstEventInstances = eventInstances.first();

                        eventInstances.not(':first').mousedown(function(e){
                            firstEventInstances.trigger(e);
                        });

                        firstEventInstances.draggable({
                            delay: 300,

                            helper: function(event){
                                var target = $(event.target);

                                var h = $('<div />');
                                h.css({ 'height': target.height(), 'width': target.width() });
                                return h;

                            },
                            start: function(event, ui){
                                BeginUserEvent();

                                draggable_info.duration = eventInstanceModel.end_min - eventInstanceModel.start_min;

                                var relTop = _picassoElement.CalcRelY(ui.position.top);
                                var left = ui.position.left + ui.helper.width() / 2;
                                draggable_info.deltaTime = _picassoData.GetTime(left, relTop) - eventInstanceModel.start_min;
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
                                var start = _picassoData.GetTime(left, relTop) - draggable_info.deltaTime;
                                start = Math.max(start, 0);
                                start = Math.min(start, 24 * 60 * periodModel.getDays().length - draggable_info.duration);

                                var inDayLeft = ui.position.left - dayModel.leftOffset * _picassoData.dayWidth;
                                inDayLeft = inDayLeft / _picassoData.dayWidth;
                                inDayLeft = Math.max(inDayLeft, 0);
                                inDayLeft = Math.min(inDayLeft, 1 - eventInstanceModel.width);
                                inDayLeft = Math.round(10 * inDayLeft) / 10;


                                eventInstanceModel.periodId = dayModel.periodId;
                                eventInstanceModel.start_min = start;
                                eventInstanceModel.end_min = start + draggable_info.duration;
                                eventInstanceModel.left = inDayLeft;

                                scope.$apply(UpdateEventViews());

                            },
                            stop: function(){
                                EndUserEvent();

                                scope.$apply(UpdateEventViews());

                                SaveEventInstance(eventInstanceModel);
                            }
                        });
                    }


                    var resizable_info = {
                        divPosition: null,
                        periodId: null,
                        lastLeft: null,
                        doLength: false,
                        doWidth: false,
                        eventInstanceOffset: null
                    };

                    function InitResizable(){

                        var resizeSEs = element.find('.event-instance .resize-se');
                        var firstResizeSE = resizeSEs.first();

                        var resizeSs = element.find('.event-instance .resize-s');
                        var firstResizeS = resizeSs.first();

                        var resizeEs = element.find('.event-instance .resize-e');
                        var firstResizeE = resizeEs.first();

                        resizeSEs.not(':first').mousedown(function(e){  firstResizeSE.trigger(e);   });
                        resizeSs.not(':first').mousedown(function(e){  firstResizeS.trigger(e);   });
                        resizeEs.not(':first').mousedown(function(e){  firstResizeE.trigger(e);   });

                        var draggableOptions =
                        {
                            delay: 300,
                            cursorAt: { left: 0, top: 0 },
                            helper: function(){ return $('<div />'); },

                            start: function(event, ui){
                                BeginUserEvent();
                                var target = $(event.target);

                                resizable_info.doLength = target.is('.resize-s, .resize-se');
                                resizable_info.doWidth = target.is('.resize-e, .resize-se');

                                resizable_info.eventInstancesOffset = _picassoElement.eventInstances.offset();
                                resizable_info.divPosition = target.closest('.event-instance').position();
                                resizable_info.periodId = _picassoData.GetDayByLeft(resizable_info.divPosition.left).periodId;
                            },
                            drag: function(event, ui){
                                var top = ui.offset.top - resizable_info.eventInstancesOffset.top + 5;
                                var left = ui.offset.left - resizable_info.eventInstancesOffset.left + 5;
                                var relTop = _picassoElement.CalcRelY(top);

                                if(resizable_info.lastLeft == null){
                                    resizable_info.lastLeft = left;
                                }

                                if(resizable_info.doLength){
                                    if(_picassoData.GetDayByLeft(left).periodId == resizable_info.periodId) {
                                        resizable_info.lastLeft = left;
                                    }

                                    var end = _picassoData.GetTime(resizable_info.lastLeft, relTop);
                                    eventInstanceModel.end_min = Math.max(end, eventInstanceModel.start_min + 15);
                                }

                                if(resizable_info.doWidth){
                                    var day = _picassoData.GetDayByLeft(resizable_info.lastLeft);
                                    var width = left - (day.leftOffset + eventInstanceModel.left) * _picassoData.dayWidth;
                                    width = width / _picassoData.dayWidth;
                                    width = Math.max(width, 0.3);
                                    width = Math.min(width, 1 - eventInstanceModel.left);
                                    width = Math.round(width * 10) / 10;

                                    eventInstanceModel.width = width;
                                }

                                if(resizable_info.doLength || resizable_info.doWidth) {
                                    scope.$digest();
                                }
                            },
                            stop: function(){
                                EndUserEvent();

                                resizable_info.doLength = false;
                                resizable_info.doWidth = false;

                                scope.$apply(UpdateEventViews());
                                SaveEventInstance(eventInstanceModel);
                            }
                        };

                        firstResizeSE.draggable(draggableOptions);
                        firstResizeS.draggable(draggableOptions);
                        firstResizeE.draggable(draggableOptions);
                    }


                    function EventView(id, day, eventInstance, isHandle){
                        this.id = id;
                        this.day = day;
                        this.eventInstance = eventInstance;
                        this.isHandle = isHandle;

                        Object.defineProperty(this, 'Border', { value: Border });
                        Object.defineProperty(this, 'Style', { value: Style });
                        Object.defineProperty(this, 'BgStyle', { value: BgStyle });
                        Object.defineProperty(this, 'TitleStyle', { value: TitleStyle });

                        Object.defineProperty(this, 'IsStart', { value: IsStart });
                        Object.defineProperty(this, 'IsEnd', { value: IsEnd });

                        Object.defineProperty(this, 'Mouseover', { value: Mouseover });
                        Object.defineProperty(this, 'Mouseleave', { value: Mouseleave });
                        Object.defineProperty(this, 'Class', { value: Class });

                        Object.defineProperty(this, 'Click', { value: Click });


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
                                zIndex: scope.hover ? 1001 : null,

                                opacity: this.isHandle ? 0 : 1
                            }
                        }

                        function Style(){
                            var dayMinOffset = 1440 * this.day.dayOffset;
                            var dayStart = dayMinOffset + _picassoData.dayStartMin;
                            var dayEnd = dayMinOffset + _picassoData.dayEndMin;

                            var start = Math.max(eventInstanceModel.start_min, dayStart);
                            var end = Math.min(eventInstanceModel.end_min, dayEnd);

                            return {
                                position: 'absolute',
                                left: (eventInstanceModel.left + this.day.leftOffset) * _picassoData.dayWidth,
                                width: eventInstanceModel.width * _picassoData.dayWidth,
                                top: (100 * _picassoData.GetOffset(start - dayMinOffset)) + '%',
                                height: (100 * _picassoData.GetLength(end - start)) + '%',

                                zIndex: scope.hover ? 1000 : eventInstanceModel.GetZIndex(),
                                opacity: this.isHandle ? 0 : 1
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

                                backgroundColor: this.eventInstance.event.category.color,
                                opacity: this.isHandle ? 0 : (scope.hover ? 0.95 : 0.75)
                            };
                        }

                        function TitleStyle() {
                            return {
                                cursor: scope.userEventIsProcessing ? 'move' : 'pointer',
                                opacity: this.isHandle ? 0 : 1
                            }
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
                            if(!picassoData.config.userEventProcessing) {
                                scope.hover = true;
                            }
                        }
                        function Mouseleave(){
                            if(!picassoData.config.userEventProcessing) {
                                scope.hover = false;
                            }
                        }

                        function Class(){
                            return picassoData.config.userEventProcessing ? 'user-event' : '';
                        }


                        var _dblclickTimeout = null;

                        function Click(event){
                            /* Double-Click Timeout
                             * Beim ersten clicken auf das Event wird dem Link nicht gefolgt (preventDefault)
                             * und es wird ein Timeout gestartet. Wenn innerhalb dieses Timeouts erneut auf
                             * das Event geclickt wird, wird dem Link normal gefolgt.
                             *
                             * Vorteil: Es funktionieren auch die Browser-Funktionen wie "neuen Tab bei Shift-Click"
                             */
                            if(_dblclickTimeout == null){
                                _dblclickTimeout = $timeout(function(){
                                    _dblclickTimeout = null;
                                }, 300);

                                event.preventDefault();
                                return false;
                            }

                            return true;
                        }
                    }


                    function SaveEventInstance(eventInstanceModel){
                        picassoData.SaveEventInstance(eventInstanceModel)
                    }
                }
            }

            return PicassoEventInstance;

        }
    ]);

})();
