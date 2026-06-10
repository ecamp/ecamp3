# ops-dashboard

This is a helm chart to deploy an oauth2-proxy and a homer dashboard.
Then the ecamp3-developers can use their github login
to see our applications like graphana and kibana.

## Prerequisites

First you need to have the following dependencies:

- jq
- kubectl (with a kubeconfig for the cluster you want to deploy to)
- helm
- helmfile

## Deployment

First, check what is currently applied:

```shell
helm -n ops-dashboard get values ops-dashboard
```

Fill in the values for .env according to .env.example

```shell
cp .env-example .env
```

you may diff the current deployment with the one you want to do now

```shell
./deploy.sh diff
```

Deploy

```shell
./deploy.sh deploy
```
