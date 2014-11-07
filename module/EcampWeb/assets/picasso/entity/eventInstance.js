/**
 * Created by pirmin on 21.10.14.
 */

CNS('ecamp.picasso.entity');

ecamp.picasso.entity.EventInstance = function EventInstanceModel($scope, eventInstance){
    this.id = eventInstance.id;
    this.periodId = eventInstance.periodId;
    this.start_min = eventInstance.start_min;
    this.end_min = eventInstance.end_min;
    this.left = eventInstance.left;
    this.width = eventInstance.width;

    this.GetHash = function(){
        var hash =
            this.id + '$' +
            this.GetEventNr() + '::' +
            '(' + this.start_min + '/' + $scope.timeline.startMin + ') - ' +
            '(' + this.end_min + '/' + $scope.timeline.endMin + ') :: [' +
            this.left + ', ' + this.width + '] :: ' +
            $scope.appearance.dayWidth;
        return hash;
    };

    this.GetDays = function(){
        var periodId = this.periodId;
        var start = this.start_min;
        var end = this.end_min;

        return $scope.days.Values.filter(function(dayModel){
            var dayStart = 1440 * dayModel.dayOffset + $scope.timeline.startMin;
            var dayEnd = 1440 * dayModel.dayOffset + $scope.timeline.endMin;

            return dayModel.periodId == periodId && dayStart < end && dayEnd > start;
        });
    };

    this.GetDayNr = function(){
        return 1 + Math.floor(this.start_min / 1440);
    };

    this.GetEventNr = function(){
        var periodId = this.periodId;
        var start = this.start_min;
        var left = this.left;
        var id = this.id;

        var countEventInstances = $scope.eventInstances.Count(function(eventInstanceModel){
            if(eventInstanceModel.id == id){ return false; }
            if(eventInstanceModel.periodId != periodId){ return false; }
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

        return countEventInstances + 1;
    };
};

ecamp.picasso.entity.EventInstance.Key = function(eventInstance){
    return eventInstance.id;
};

ecamp.picasso.entity.EventInstance.Insert = function($scope, eventInstance){
    return new ecamp.picasso.entity.EventInstance($scope, eventInstance);
};

ecamp.picasso.entity.EventInstance.Update = function(eventInstanceModel, eventInstance){
    eventInstanceModel.id = eventInstance.id;
    eventInstanceModel.periodId = eventInstance.periodId;
    eventInstanceModel.start_min = eventInstance.start_min;
    eventInstanceModel.end_min = eventInstance.end_min;
    eventInstanceModel.left = eventInstance.left;
    eventInstanceModel.width = eventInstance.width;
    return eventInstanceModel;
};

ecamp.picasso.entity.EventInstance.Sort = function(ei1, ei2){

    if(ei1.id == ei2.id){
        return 0;
    }

    return (ei1.id < ei2.id) ? -1 : 1;


    var diff = ei1.start_min - ei2.start_min;
    if(diff == 0){
        diff = ei1.left - ei2.left;
    }
    return diff;
};