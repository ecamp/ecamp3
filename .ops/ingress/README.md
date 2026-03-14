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

And you need to log in to the GitHub ocr registry.
For that you need a GitHub personal access token with the following scopes:
`Read access to artifact metadata, code, and metadata`

```shell
helm registry login -u $GITHUB_USERNAME ghcr.io
```

### CRD

Somehow the upgrade command did not install the CRD.
I did that manually:

```shell
cd /tmp
mkdir nginx-ingress
cd nginx-ingress
helm pull oci://ghcr.io/nginx/charts/nginx-ingress --untar --version 2.4.4
# Pulled: ghcr.io/nginx/charts/nginx-ingress:2.4.4
# Digest: sha256:ab4376a132fd44c4aaae1d60069d71a8315be0bc747b7a3c5af56537da579680
cd nginx-ingress/crds
kubectl apply -f ./
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
