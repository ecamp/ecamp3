# ecamp3-logging

This is a helm chart to deploy an EFK Stack (Elasticsearch, Filebeat, Kibana) to a cluster where
ecamp3 is running.

## Info

It is set up according to: https://www.elastic.co/guide/en/cloud-on-k8s/master/k8s-install-helm.html

## Prerequisites

You need to add the elastic helm repository:

```shell
helm repo add elastic https://helm.elastic.co
helm repo update
```
Then you need to install the eck-operator in the cluster

```shell
helm install elastic-operator elastic/eck-operator -n elastic-system --create-namespace
```
