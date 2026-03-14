# external-dns-gateway-api

This is a helm chart to deploy `external-dns` configured to use the Kubernetes Gateway API (`HTTPRoute`) as its source.

## Prerequisites

You need `kubectl`, `helm`, and `helmfile` installed.

## Deployment

To diff the current deployment:

```shell
./deploy.sh diff
```

To deploy:

```shell
./deploy.sh deploy
```

## Configuration

The deployment uses `helmfile` to manage dependencies and values. The configuration is defined in:

- `helmfile.yaml`: Main helmfile configuration.
- `values.yaml.gotmpl`: Helm values template.
- `env.yaml`: Environment-specific values (merged from GitHub Action secrets/vars).

### ExternalDNS and Gateway API

By setting `sources: ["gateway-httproute"]`, `external-dns` will specifically watch for `HTTPRoute` resources (part of the Kubernetes Gateway API) and create DNS records for the hostnames defined in those routes.

It will automatically create `A` or `CNAME` records in the configured DNS provider (e.g., Cloudflare) for hostnames pointing to the Gateway's external IP.

### Authentication

Authentication for the DNS provider (e.g., `CLOUDFLARE_API_TOKEN`) should be provided via environment variables or secrets, which are merged into `env.yaml` during the deployment process.
