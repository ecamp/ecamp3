/**
 * Created by pirmin on 21.10.14.
 */

CNS('ecamp.picasso.entity');

ecamp.picasso.entity.Period = function(period){
    this.id = period.id;
    this.start = period.start;
    this.visible = true;
};

ecamp.picasso.entity.Period.Key = function(period){
    return period.id;
};

ecamp.picasso.entity.Period.Insert = function(period){
    return new ecamp.picasso.entity.Period(period);
};

ecamp.picasso.entity.Period.Update = function(periodModel, period){
    periodModel.start = period.start;
    return periodModel;
};

ecamp.picasso.entity.Period.IsVisible = function(period){
    return period.visible;
};

ecamp.picasso.entity.Period.Sort = function(p1, p2) {
    return p1.start.getTime() - p2.start.getTime();
};
