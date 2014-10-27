/**
 * Created by pirmin on 21.10.14.
 */

CNS('ecamp.picasso.entity');

ecamp.picasso.entity.EventInstance = function EventInstanceModel($scope, eventInstance){
    this.id = eventInstance.id;
    this.periodId = eventInstance.periodId;
    this.start = eventInstance.start;
    this.end = eventInstance.end;
    this.left = 0;
    this.width = 1;

    this.GetHash = function(){
        var hash =
            this.id + '$' +
            this.GetEventNr() + '::' +
            '(' + this.start + '/' + $scope.timeline.startMin + ') - ' +
            '(' + this.end + '/' + $scope.timeline.endMin + ') :: [' +
            this.left + ', ' + this.width + '] :: ' +
            $scope.appearance.dayWidth;
        return hash;
    };

    this.GetDays = function(){
        var periodId = this.periodId;
        var start = this.start;
        var end = this.end;

        return $scope.days.Values.filter(function(dayModel){
            var dayStart = 1440 * dayModel.dayOffset + $scope.timeline.startMin;
            var dayEnd = 1440 * dayModel.dayOffset + $scope.timeline.endMin;

            return dayModel.periodId == periodId && dayStart < end && dayEnd > start;
        });
    };

    this.GetEventNr = function(){
        var periodId = this.periodId;
        var start = this.start;
        var left = this.left;
        var id = this.id;

        var countEventInstances = $scope.eventInstances.Count(function(eventInstanceModel){
            if(eventInstanceModel.id == id){ return false; }
            if(eventInstanceModel.periodId != periodId){ return false; }
            if(eventInstanceModel.start > start){ return false; }

            if(Math.floor(eventInstanceModel.start / 1440) == Math.floor(start / 1440)){
                if(eventInstanceModel.start < start){ return true; }
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
    eventInstanceModel.start = eventInstance.start;
    eventInstanceModel.end = eventInstance.end;
    return eventInstanceModel;
};

ecamp.picasso.entity.EventInstance.Sort = function(ei1, ei2){
    var diff = ei1.start - ei2.start;
    if(diff == 0){
        diff = ei1.left - ei2.left;
    }
    return diff;
};