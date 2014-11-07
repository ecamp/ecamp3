/**
 * Created by pirmin on 17.08.14.
 */

(function(ngApp){
    var SortedDictionary = ecamp.core.util.SortedDictionary;

    function PicassoController
    ( $scope
    , $timeout
    , PicassoData
    , PicassoTimeline
    , PicassoEventInstance
    , PicassoEventCreate
    ){

        $scope.log = function(){ console.log.apply(console, arguments) };

        var picassoElement = null;
        var picassoTimeline = null;
        var picassoEventInstance = null;
        var picassoEventCreate = null;

        var picassoData = new PicassoData();
        picassoData.LoadCamp('5dfd331c').then(RefreshPicasso);

        Object.defineProperty($scope, 'periods', { get: function(){ return picassoData.periods.Values; } });
        Object.defineProperty($scope, 'dates', { get: function(){ return picassoData.dates.Values; } });
        Object.defineProperty($scope, 'days', { get: function(){ return picassoData.days.Values; } });
        Object.defineProperty($scope, 'eventInstances', { get: function(){ return picassoData.eventInstances.Values; } });

        Object.defineProperty($scope, 'config', { get: function(){ return picassoData.config; } });
        Object.defineProperty($scope, 'fullScreenClass', { get: function(){ return picassoData.fullScreenClass; } });

        Object.defineProperty($scope, 'timeline', { get: function(){ return picassoTimeline; } });
        Object.defineProperty($scope, 'createEvent', { get: function(){ return picassoEventCreate; } });

        var userEventIsProcessing = false;
/*
        setInterval(function(){
            if(!userEventIsProcessing) {
                $scope.remoteData.Update(function(){
                    if(!userEventIsProcessing) {
                        RefreshData();
                    }
                });
            }
        }, 5000);
*/

        $scope.$watch('config.timeRange', function(){
            userEventIsProcessing = true;
            RefreshPicasso();
            $timeout(function(){ userEventIsProcessing = false; });
        });

        $scope.$watch('config.isFullscreen', RefreshFullScreen);

        $(window).resize(ThrottleScopeApplys(function(){
            RefreshTimeline();
            RefreshAppearance();
        }, 200));


        Object.defineProperty(this, 'Init', { value: Init });
        function Init(element){
            picassoElement = new PicassoElement(element);
            picassoTimeline = new PicassoTimeline(picassoElement);
            picassoEventInstance = new PicassoEventInstance(picassoData, picassoElement);

            picassoEventCreate = new PicassoEventCreate(picassoData, picassoElement);

            picassoElement.eventInstances.mousedown(function(event){
                picassoEventCreate.MouseDown(event);
                if(picassoEventCreate.isCreating){ $scope.$digest(); }
            });
            picassoElement.eventInstances.mousemove(function(event){
                if(picassoEventCreate.isCreating){
                    picassoEventCreate.MouseMove(event);
                    $scope.$digest();
                }
            });
            picassoElement.eventInstances.mouseup(function(event){
                if(picassoEventCreate.isCreating){
                    picassoEventCreate.MouseUp(event);
                    $scope.$digest();
                }
            });

            //console.log(picassoElement.eventInstances);

            RefreshTimeline();
            //InitEventCreateHelper();
            RefreshPicasso();


            Object.defineProperty(this, 'LinkEventInstance', {
                value: picassoEventInstance.Link
            });
        }


        function RefreshFullScreen(){
            if(picassoElement == null) return;

            $timeout(function(){
                RefreshTimeline();
                RefreshAppearance();
            });
        }

        function RefreshPicasso(){
            if(picassoElement == null) return;

            RefreshTimeline();
            RefreshAppearance();
        }

        function RefreshAppearance(){
            if(picassoElement == null) return;

            // Refresh DayWidth:
            var dayCount = Math.max(1, picassoData.days.Count());
            picassoData.dayWidth = picassoElement.picassoBodyWidth / dayCount;
        }

        function RefreshTimeline(){
            if(picassoTimeline == null) return;

            picassoTimeline.BeginUpdate();
            picassoTimeline.height = picassoElement.eventInstanceHeight;
            picassoTimeline.startMin = picassoData.config.dayStartMin;
            picassoTimeline.endMin = picassoData.config.dayEndMin;
            picassoTimeline.EndUpdate();
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
    }


    function PicassoElement(element){
        var _element = $(element);

        var _picassoBody = _element.find('.picasso-body');
        var _eventInstances = _element.find('.event-instances');

        Object.defineProperty(this, 'picassoBody', { value: _picassoBody });
        Object.defineProperty(this, 'picassoBodyWidth', { get: function(){ return _picassoBody.width(); } });
        Object.defineProperty(this, 'picassoBodyHeight', { get: function(){ return _picassoBody.height(); } });

        Object.defineProperty(this, 'eventInstances', { value: _eventInstances });
        Object.defineProperty(this, 'eventInstanceWidth', { get: function(){ return _eventInstances.width(); } });
        Object.defineProperty(this, 'eventInstanceHeight', { get: function(){ return _eventInstances.height(); } });

        Object.defineProperty(this, 'CalcRelY', {
            value: function(value){ return value / this.eventInstanceHeight; }
        });
    }



    ngApp.directive('picasso', [function(){
        return {
            restrict: 'E',
            templateUrl: '/web-assets/picasso/tpl/picasso.html',
            scope: {},
            controller: PicassoController,

            link: function($scope, $element, $attrs, $ctrl){
                $ctrl.Init($element);

                $element.find('.picasso-body>div').scroll(function(e){
                    var $target = $(e.target);
                    $target.scrollTop(0);
                });
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
            }
        };
    }]);

    /*
    ngApp.directive('animateStyle', [function(){
        return {
            restrict: 'A',
            link: function($scope, $element, $attrs, $ctrl){
                $scope.$watch($attrs.animateStyle, function(newStyles, oldStyles){
                    / *
                    if (oldStyles && (newStyles !== oldStyles)) {
                        var keys = Object.keys(oldStyles);
                        for(var idx = 0; idx < keys.length; idx++){
                            $element.css(keys[idx], '');
                        }
                    }
                    * /
                    if (newStyles) $element.animate(newStyles, 500);
                }, true);
            }
        };
    }]);
    */

})(window.ecamp.ngApp);
