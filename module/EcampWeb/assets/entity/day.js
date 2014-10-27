/**
 * Created by pirmin on 21.10.14.
 */

CNS('ecamp.entity');

ecamp.entity.Day = function(id, periodId, dayOffset, date){
    this.id = id;
    this.periodId = periodId;
    this.dayOffset = dayOffset;
    this.date = date;
};

ecamp.entity.Day.Sort = function(day1, day2){
    var diff = day1.date - day2.date;
    if(diff == 0){
        diff = day1.periodId - day2.periodId;
    }
    return diff;
};