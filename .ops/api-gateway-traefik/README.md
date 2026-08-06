# api-gateway-traefik

This is a helm chart to deploy the Traefik API Gateway.

## Prerequisites

You need the helm repository:

```shell
helm repo add traefik https://traefik.github.io/charts
helm repo update
```

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

The deployment uses the standard Helm chart for Traefik with the following key configurations:

- `providers.kubernetesGateway.enabled: true`: Enables the Kubernetes Gateway API provider.
- `gateway.enabled: true`: Deploys the Gateway and GatewayClass resources.
- `logs.access.enabled: true`: Enables access logging in JSON format.

The configuration is defined in:

- `Chart.yaml`: Helm chart metadata and dependencies.
- `values.yaml`: Default values for the Traefik chart.
- `deploy.sh`: Shell script for automated deployment.
