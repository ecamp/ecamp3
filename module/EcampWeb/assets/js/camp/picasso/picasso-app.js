/**
 * Created by pirmin on 17.08.14.
 */

(function(){

    var module = angular.module('ecamp.picasso.app', [
        'pascalprecht.translate',
        'ecamp-modal-form',
        'ecamp.picasso.data',
        'ecamp.picasso.timeline',
        'ecamp.picasso.event-instance',
        'ecamp.picasso.event-create'
    ]);


    function PicassoController(
        $scope, $timeout, $translate, $asyncModal, PicassoData, PicassoTimeline, PicassoEventInstance, PicassoEventCreate
    ){

        var picassoData = new PicassoData();
        var picassoElement = null;
        var picassoTimeline = null;
        var picassoEventInstance = null;
        var picassoEventCreate = null;

        Object.defineProperty($scope, 'periods', { get: function(){ return picassoData.periods.Values; } });
        Object.defineProperty($scope, 'dates', { get: function(){ return picassoData.dates.Values; } });
        Object.defineProperty($scope, 'days', { get: function(){ return picassoData.days.Values; } });
        Object.defineProperty($scope, 'eventInstances', { get: function(){ return picassoData.eventInstances.Values; } });

        Object.defineProperty($scope, 'config', { get: function(){ return picassoData.config; } });
        Object.defineProperty($scope, 'fullScreenClass', { get: function(){ return picassoData.fullScreenClass; } });

        Object.defineProperty($scope, 'timeline', { get: function(){ return picassoTimeline; } });
        Object.defineProperty($scope, 'createEvent', { get: function(){ return picassoEventCreate; } });

        Object.defineProperty($scope, 'DeleteEventInstance', { value: DeleteEventInstance });
        Object.defineProperty($scope, 'EditEventInstance', { value: EditEventInstance });

        // var userEventIsProcessing = false;

        setInterval(function(){
            $timeout(function(){
                if(!picassoData.config.userEventProcessing){
                    picassoData.remoteData.Update(function () {
                        if(!picassoData.config.userEventProcessing) {
                            picassoData.RefreshCamp();
                        }
                    });
                }
            });
        }, 15000);


        $scope.$watch('campId', function(campId){
            picassoData.LoadCamp(campId).then(RefreshPicasso);
        });

        $scope.$watch('config.timeRange', function(){
            try {
                picassoData.config.beginUserEvent();
                RefreshPicasso();
            } finally {
                $timeout(function(){ picassoData.config.endUserEvent(); });
            }
        });

        $scope.$watch('config.isFullscreen', RefreshFullScreen);

        $(window).resize(ThrottleScopeApplys(function(){
            try {
                $scope.$apply(picassoData.config.beginUserEvent());

                RefreshTimeline();
                RefreshAppearance();
            } finally {
                $timeout(function(){ picassoData.config.endUserEvent(); });
            }
        }, 200));


        Object.defineProperty(this, 'Init', { value: Init });
        function Init(element){
            picassoElement = new PicassoElement(element);
            picassoTimeline = new PicassoTimeline(picassoElement);
            picassoEventInstance = new PicassoEventInstance(picassoData, picassoElement);
            Object.defineProperty(this, 'LinkEventInstance', { value: picassoEventInstance.Link });

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

            RefreshTimeline();
            //RefreshPicasso();
        }


        function RefreshFullScreen(){
            if(picassoElement == null) return;

            $timeout(function(){
                try {
                    $scope.$apply(picassoData.config.beginUserEvent());

                    RefreshTimeline();
                    RefreshAppearance();
                } finally {
                    $timeout(function(){ picassoData.config.endUserEvent(); });
                }
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


        function DeleteEventInstance(eventInstanceModel){
            var modalInstance = $asyncModal.open({
                templateUrl: 'deleteEventDialog.html',
                controller: function($scope, $modalInstance){
                    $scope.eventInstance = eventInstanceModel;

                    $scope.ok = function(){ $modalInstance.close(); };
                    $scope.cancel = function(){ $modalInstance.dismiss('cancel'); };
                }
            });

            modalInstance.result.then(function(){
                picassoData.DeleteEventInstance(eventInstanceModel);
            });
        }

        function EditEventInstance(eventInstanceModel){
            var url = URI.expand($translate.instant('URL_CAMP_UPDATE_EVENT_INSTANCE'), {
                campId: picassoData.camp.id
            });
            url.query({ 'eventInstanceId': eventInstanceModel.id });

            var dlg = $asyncModal.open({
                cache: false,
                templateUrl: url.href()
            });

            dlg.result.then(function(){
                picassoData.RefreshEventInstance(eventInstanceModel);
            });
        }


        function ThrottleScopeApplys(fn, delay){
            var idx = Number.NaN;
            return function(){
                if(idx) clearTimeout(idx);
                idx = setTimeout(function(){
                    idx = Number.NaN;
                    fn();
                    $scope.$apply();
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



    module.directive('picasso', [function(){
        return {
            restrict: 'E',
            templateUrl: '/web-assets/tpl/camp/picasso/picasso.html',
            scope: {
                campId: "@"
            },
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

    module.directive('eventInstance', [function(){
        return {
            restrict: 'E',
            require: '^picasso',
            templateUrl: '/web-assets/tpl/camp/picasso/event-instance.html',
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

})();
