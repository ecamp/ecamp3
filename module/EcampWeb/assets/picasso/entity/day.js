/**
 * Created by pirmin on 21.10.14.
 */

CNS('ecamp.picasso.entity');

ecamp.picasso.entity.Day = function($scope, day){
    this.id = day.id;
    this.periodId = day.periodId;
    this.dayOffset = day.dayOffset;
    this.dayNr = day.dayOffset + 1;
    this.date = day.date;
    this.leftOffset = 0;

    this.style = function(){
        return {
            left: this.leftOffset * $scope.appearance.dayWidth,
            width: $scope.appearance.dayWidth
        };
    };
};

ecamp.picasso.entity.Day.Key = function(day){
    return day.id;
};

ecamp.picasso.entity.Day.Insert = function($scope, day){
    return new ecamp.picasso.entity.Day($scope, day);
};

ecamp.picasso.entity.Day.Update = function(dayModel, day){
    dayModel.periodId = day.periodId;
    dayModel.dayOffset = day.dayOffset;
    dayModel.dayNr = day.dayOffset + 1;
    dayModel.date = day.date;
    return dayModel;
};

ecamp.picasso.entity.Day.Sort = function(d1, d2){
    /*
     var diff = SortId(d1.periodId, d2.periodId);
     if(diff == 0){
     diff = d1.date.getTime() - d2.date.getTime();
     }
     return diff;
     */

    var diff = d1.date.getTime() - d2.date.getTime();
    if(diff == 0){
        diff =  d1.periodId - d2.periodId;
    }
    return diff;
};