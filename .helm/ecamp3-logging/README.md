# ecamp3-logging

This is a helm chart to deploy the logging stack of ecamp3.
It uses the EFK stack (Elasticsearch, Filebeat and Kibana).
There is currently no setup to deploy it in a continuous fashion.\
The [deploy.sh](deploy.sh) allows to deploy the chart if you have
a working [kubectl and helm setup](https://github.com/ecamp/ecamp3/wiki/Deployment-installation-Kubernetes#connecting-to-the-cluster).

The current setup of kibana is stored here: [kibana-objects.ndjson](files%2Fkibana%2Fkibana-objects.ndjson)\
It can be generated and restored with the scripts [dump-kibana-objects.sh](files%2Fkibana%2Fdump-kibana-objects.sh)
and [restore-kibana-objects.sh](files%2Fkibana%2Frestore-kibana-objects.sh)
