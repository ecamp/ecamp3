/**
 * Created by pirmin on 21.10.14.
 */

CNS('ecamp.entity');

ecamp.entity.Period = function(id, campId, start, desc, color){
    this.id = id;
    this.campId = campId;
    this.start = start;
    this.description = desc;
    this.color = color;

    this.visible = true;
};