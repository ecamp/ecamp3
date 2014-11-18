/**
 * Created by pirmin on 21.10.14.
 */

CNS('ecamp.entity');

ecamp.entity.EventInstance = function(id, eventId, periodId, start, end){
    this.id = id;
    this.eventId = eventId;
    this.periodId = periodId;
    this.start = start;
    this.end = end;
};