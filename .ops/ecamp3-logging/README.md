# ecamp3-logging

This is a helm chart to deploy an EFFK Stack (Elasticsearch, fluentbit, fluentd, Kibana) to a cluster where
ecamp3 is running.

## Prerequisites

You need to add the fluent helm repository:

```shell
helm repo add fluent https://fluent.github.io/helm-charts 
helm repo update
```

## Provisioning of Kibana Configuration

There are 2 scripts to help create a consistent configuration between
different clusters. You need [curl](https://github.com/curl/curl) and [jq](https://github.com/jqlang/jq) for the scripts
to work.

To store the current dashboard, index-pattern and search in [kibana-objects.ndjson](files%2Fkibana%2Fkibana-objects.ndjson),
you can do the following:

```shell
kubectl -n ecamp3-logging port-forward services/kibana 5601:5601
sh files/kibana/dump-kibana-objects.sh
```

To restore [kibana-objects.ndjson](files%2Fkibana%2Fkibana-objects.ndjson) to a cluster, you can do the follwing:

```shell
kubectl -n ecamp3-logging port-forward services/kibana 5601:5601
sh files/kibana/restore-kibana-objects.sh
```
