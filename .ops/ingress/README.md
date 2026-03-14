# ops-dashboard

This is a helm chart to deploy an oauth2-proxy and a homer dashboard.
Then the ecamp3-developers can use their github login
to see our applications like graphana, kibana, kubernetes-dashboard...

## Prerequisites

You need the helm repositories:

```shell
helm repo add ingress-nginx https://kubernetes.github.io/ingress-nginx
helm repo add traefik https://traefik.github.io/charts
helm repo add external-dns https://kubernetes-sigs.github.io/external-dns/
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

## ExternalDNS Configuration

`external-dns` is used to automatically synchronize Kubernetes resources with external DNS providers (in this case, Cloudflare). It monitors specific resources and creates DNS records based on the hostnames defined in them.

### Integration with Gateway API

By setting `sources: ["gateway-httproute"]`, `external-dns` will specifically watch for `HTTPRoute` resources (part of the Kubernetes Gateway API) and create DNS records for the hostnames defined in those routes.

It will then automatically create an `A` or `CNAME` record in Cloudflare for hostnames pointing to the Traefik Gateway's external IP.

### Authentication

Actual authentication (API tokens/email) needs to be provided via `extraArgs` or Kubernetes secrets during deployment.

### Transition Period

The current `external-dns` configuration is focused on the new Gateway API (`gateway-httproute`). If you still need to support legacy `Ingress` resources during the transition period, you might need to add `ingress` back to the `sources` list in `values.yaml`.
