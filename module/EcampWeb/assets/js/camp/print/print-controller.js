(function(){

    var JOB_STATUS_WAITING = 1;
    var JOB_STATUS_DELAYED = 2;
    var JOB_STATUS_RUNNING = 3;
    var JOB_STATUS_COMPLETE = 4;
    var JOB_STATUS_CANCELLED = 5;
    var JOB_STATUS_FAILED = 6;


    var module = angular.module('ecamp.camp.print', [
        'ecamp.api.v0'
    ]);

    module.controller('PrintController', [
        '$document', '$scope', '$http', 'CampId', 'CampPrintConfig', 'ApiV0.ResqueJobs', 'ApiV0.Camps',
        function($document, $scope, $http, CampId, CampPrintConfig, ResqueJobs, ApiCamps)
        {
            function TitlePage(){
                this.Initialize = function(){ };
                this.Serialize = function(){ return {}; };
                this.Deserialize =  function(data){ };

                this.GetPage = function(){
                    return {
                        'type': 'PAGE',
                        'items': [{
                            'name': 'Cover',
                            'campId': CampId
                        }]
                    };
                };
            }
            TitlePage.title = 'Title page';
            TitlePage.template = 'title-page';


            function TableOfContents(){
                this.Initialize = function(){ };
                this.Serialize = function(){ return {}; };
                this.Deserialize =  function(data){ };

                this.GetPage = function(){
                    return {
                        'type': 'TOC'
                    };
                };
            }
            TableOfContents.title = 'Table of contents';
            TableOfContents.template = 'table-of-contents';


            function CollaborationList(){
                this.Initialize = function(){ };
                this.Serialize = function(){ return {}; };
                this.Deserialize =  function(data){ };

                this.GetPage = function(){
                    return {
                        'type': 'PAGE',
                        'items': [{
                            'name': 'CollaborationList',
                            'campId': CampId
                        }]
                    };
                };
            }
            CollaborationList.title = 'Collaboration list';
            CollaborationList.template = 'collaborators-list';


            function Picasso(){
                this.periods = [];

                this.Initialize = function(){
                    this.periods = $scope.periods.map(function(p){ return p.id; });
                };
                this.Serialize = function(){
                    return { periods: this.periods };
                };
                this.Deserialize = function(data){
                    this.periods = data.periods;
                };

                this.TogglePeriod = function(period){
                    var idx = this.periods.indexOf(period.id);
                    if(idx === -1){
                        this.periods.push(period.id);
                    } else {
                        this.periods.splice(idx, 1);
                    }
                    $scope.saveConfig();
                };

                this.GetPage = function(){
                    var items = [];
                    angular.forEach(this.periods, function(period){
                        items.push({
                            'name': 'Picasso',
                            'periodId': period
                        });
                    });

                    return {
                        'type': 'PAGE',
                        'items': items
                    };
                };
            }
            Picasso.title = 'Picasso';
            Picasso.template = 'picasso';


            function DetailedProgram(){
                this.periods = [];

                this.Initialize = function(){
                    this.periods = $scope.periods.map(function(p){ return p.id; });
                };
                this.Serialize = function(){
                    return { periods: this.periods };
                };
                this.Deserialize = function(data){
                    this.periods = data.periods;
                };

                this.TogglePeriod = function(period){
                    var idx = this.periods.indexOf(period.id);
                    if(idx === -1){
                        this.periods.push(period.id);
                    } else {
                        this.periods.splice(idx, 1);
                    }
                    $scope.saveConfig();
                };

                this.GetPage = function(){
                    var items = [];
                    angular.forEach(this.periods, function(period){
                        items.push({
                            'name': 'DetailedProgram',
                            'periodId': period
                        });
                    });

                    return {
                        'type': 'PAGE',
                        'items': items
                    };
                };
            }
            DetailedProgram.title = 'Detailed program';
            DetailedProgram.template = 'detailed-program';


            var ItemPrototypes = {
                TitlePage: TitlePage,
                TableOfContents: TableOfContents,
                CollaborationList: CollaborationList,
                Picasso: Picasso,
                DetailedProgram: DetailedProgram
            };

            $scope.catalog = [
                TitlePage,
                TableOfContents,
                CollaborationList,
                Picasso,
                DetailedProgram
            ];



            $scope.camp = null;
            $scope.periods = [];

            ApiCamps.$get(CampId).then(function(camp){
                $scope.camp = camp;
                $scope.camp.$get('periods').then(function(list){
                    list.$get('items').then(function(periods){
                        $scope.periods = periods;
                    });
                });
            });



            $scope.items = [];
            $scope.jobs = [];



            $scope.addItem = function(constructor){
                var item = new constructor();
                item.Initialize();
                $scope.items.push(item);
                $scope.saveConfig();
            };

            $scope.removeItem = function(idx){
                $scope.items.splice(idx, 1);
                $scope.saveConfig();
            };

            $scope.loadConfig = function(){
                $http.get(CampPrintConfig).success(function(data, status){
                    angular.forEach(data, function(obj){
                        var name = obj.name;
                        var item = new (ItemPrototypes[name])();
                        item.Deserialize(obj.data);

                        $scope.items.push(item);
                    });
                });
            };

            $scope.saveConfig = function(){
                var data = [];
                angular.forEach($scope.items, function(item){
                    data.push({
                        'name': item.constructor.name,
                        'data': item.Serialize()
                    });
                });

                $http.post(CampPrintConfig, data);
            };




            $scope.createPrintJob = function(){
                var data = {
                    'name': 'CreatePdf',
                    'pages': []
                };

                $scope.items.forEach(function(item){
                    data['pages'].push(item.GetPage());
                });

                ResqueJobs.$post({}, data)
                    .then(function(job) {
                        $scope.jobs.push({
                            'disable': true,
                            'download': '',
                            'job': job
                        });
                    });
            };

            function UpdateJobs(){
                for(var idx = 0; idx < $scope.jobs.length; idx++){
                    UpdateJob($scope.jobs[idx]);
                }
            }

            function UpdateJob(jobItem){
                if(jobItem.job.status == JOB_STATUS_WAITING || jobItem.job.status == JOB_STATUS_RUNNING) {
                    jobItem.job.$get('self').then(function(job){
                        jobItem.job = job;

                        if(job.status == JOB_STATUS_COMPLETE){
                            jobItem.disable = false;
                            jobItem.download = job.$href('result');
                            DownloadPdf(jobItem);
                        }
                    });
                }
            }

            function DownloadPdf(jobItem){
                var iFrm = angular.element('<iframe></iframe>');
                iFrm.attr('src', jobItem.download);
                iFrm.css('display', 'none');

                $document.find('body').append(iFrm);
            }



            $scope.loadConfig();
            setInterval(UpdateJobs, 3000);
        }
    ]);

})();