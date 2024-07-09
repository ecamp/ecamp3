# ops-dashboard

This is a helm chart to deploy an oauth2-proxy and a homer dashboard.
Then the ecamp3-developers can use their github login
to see our applications like graphana, kibana, kubernetes-dashboard...

## Prerequisites

You need the oauth2-proxy helm chart:

```shell
helm repo add oauth2-proxy https://oauth2-proxy.github.io/manifests
helm repo update
```

You also need the kubernetes-dashboard helm chart:

```shell
helm repo add kubernetes-dashboard https://kubernetes.github.io/dashboard/
helm repo update
```

## Deployment

First, make sure you don't overwrite the configuration currently applied:

```shell
helm -n ops-dashboard get values ops-dashboard
```

Fill in the values for values.access.yaml according to demo.values.access.yaml

```shell
cp demo.values.access.yaml values.access.yaml 
```

To diff the deployment
```shell
helm template \
    --namespace ops-dashboard --no-hooks --skip-tests \
    ops-dashboard . \
    --values values.yaml \
    --values values.access.yaml | kubectl diff --namespace ops-dashboard -f - | batcat -l diff -
```
