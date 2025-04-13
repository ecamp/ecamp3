# ops-dashboard

This is a helm chart to deploy an oauth2-proxy and a homer dashboard.
Then the ecamp3-developers can use their github login
to see our applications like graphana, kibana, kubernetes-dashboard...

## Prerequisites

You need the ingress-nginx helm chart:

```shell
helm repo add ingress-nginx https://kubernetes.github.io/ingress-nginx
helm repo update
```

## Deployment

First, check what is currently applied:

```shell
helm -n ingress-nginx get values ingress-nginx
```

you may diff the current deployment with the one you want to do now

```shell
./deploy.sh diff
````

Deploy

```shell
./deploy.sh deploy
```
