/**
 * Created by pirmin on 21.10.14.
 */

CNS('ecamp.picasso.entity');

ecamp.picasso.entity.Period = function($scope, period){
    this.id = period.id;
    this.start = period.start;
    this.visible = true;

    this.getDays = function(){
        var periodId = this.id;

        return $scope.days.Values.filter(function(dayModel){
            return dayModel.periodId == periodId;
        });
    };
};

ecamp.picasso.entity.Period.Key = function(period){
    return period.id;
};

ecamp.picasso.entity.Period.Insert = function($scope, period){
    return new ecamp.picasso.entity.Period($scope, period);
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
