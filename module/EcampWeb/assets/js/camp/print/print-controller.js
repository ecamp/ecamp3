(function(){

    var module = angular.module('ecamp.camp.print', []);

    module.controller('PrintController', [
        '$document', '$scope', '$timeout', '$http',
        function($document, $scope, $timeout, $http)
        {

            function UpdateJobs(){
                for(var idx = 0; idx < $scope.jobs.length; idx++){
                    UpdateJob($scope.jobs[idx]);
                }
            }

            function UpdateJob(job){
                if(job.state == 'waiting' || job.state == 'creating') {
                    $http.get(job.url).success(function (data, status) {
                        job.url = data.url;
                        job.state = data.state;

                        if(job.state == 'created'){
                            job.disable = false;
                            DownloadPdf(job);
                        }
                    });
                }
            }

            function DownloadPdf(job){
                var iFrm = angular.element('<iframe></iframe>');
                iFrm.attr('src', job.url);
                iFrm.css('display', 'none');

                $document.find('body').append(iFrm);
            }


            var _configUrl = '';
            var _pdfUrl = '';

            $scope.catalog = [
                {
                    'name': 'Title page',
                    'template': 'title-page',
                    'page': {
                        'type': 'PAGE',
                        'items': [{
                            'name': 'Cover',
                            'campId': ''
                        }]
                    }
                }, {
                    'name': 'Table of contents',
                    'template': 'table-of-contents',
                    'page': {
                        'type': 'TOC'
                    }
                }, {
                    'name': 'Collaborators list',
                    'template': 'collaborators-list',
                    'page': {
                        'type': ''
                    }
                }, {
                    'name': 'Picasso',
                    'template': 'picasso',
                    'page': {
                        'type': 'PAGE',
                        'items': [{
                            'name': 'Picasso'
                        }]
                    }
                }, {
                    'name': 'Detailed program',
                    'template': 'detailed-program'

                }
            ];

            $scope.state = 'initial';
            $scope.items = [];
            $scope.jobs = [];

            setInterval(UpdateJobs, 3000);

            $scope.init = function(configUrl, pdfUrl){
                _configUrl = configUrl;
                _pdfUrl = pdfUrl;

                $scope.loadConfig();
            };

            $scope.addItem = function(item){
                $scope.items.push(angular.copy(item));
                $scope.saveConfig();
            };

            $scope.removeItem = function(idx){
                $scope.items.splice(idx, 1);
                $scope.saveConfig();
            };



            $scope.loadConfig = function(){
                $http.get(_configUrl).success(
                    function(data, status){
                        $scope.items = data;
                    }
                );
            };

            $scope.saveConfig = function(){
                $http.post(_configUrl, $scope.items);
            };



            $scope.createPrintJob = function(){
                $scope.state = 'create-job';

                var data = {
                    'name': 'CreatePdf',
                    'pages': []
                };

                $scope.items.forEach(function(item){
                    data['pages'].push(angular.copy(item.page));
                });

                $http
                    .post(_pdfUrl, data)
                    .success(function(data, status){
                        data.disable = true;
                        $scope.jobs.push(data);
                    })
                    .error(function(data, status){
                    })
                ;
            };



            $scope.pdfUrl = function(){
                return _pdfUrl;
            };
        }
    ]);

})();