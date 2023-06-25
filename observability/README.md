# Observability

## Kubernetes Monitoring Stack

If you have deployed to a DOKS (Digital Ocean Kubernetes) you can make use of their [Kubernetes Monitoring Stack](https://marketplace.digitalocean.com/apps/kubernetes-monitoring-stack) available
from the Marketplace. This will deploy Prometheus Operator, Grafana and Alertmanager.

### PostgreSQL data source

By default only Prometheus is configured as a data source in Grafana. You can add a PostgreSQL
data source so that you can query the database directly from Grafana. Use the database connection
credentials from your PostgresSQL instance and provide a CA certificate if necessary
(choose `verify-ca` as 'TLS/SSL Mode' and supply the certificate into 'TLS/SSL Root Certificate').
It is recommended to use a database user with read-only permissions.

Documentation: <https://grafana.com/docs/grafana/latest/datasources/postgres>

### Configuring Service Monitors for Prometheus

You can add additional monitoring targets by following the instructions [here](https://marketplace.digitalocean.com/apps/kubernetes-monitoring-stack). A more in-depth explanation is
available [here](https://github.com/digitalocean/Kubernetes-Starter-Kit-Developers/blob/main/04-setup-observability/prometheus-stack.md#step-2---configure-prometheus-and-grafana).

To update the `kube-prometheus-stack` helm Chart with your additional service monitor configuration
you can use the following command:

```bash
helm upgrade kube-prometheus-stack prometheus-community/kube-prometheus-stack \
--version <HELM_CHART_VERSION> --namespace kube-prometheus-stack -f prometheus-values-<environment>.yml
```

where `HELM_CHART_VERSION` matches the currently deployed version of your `kube-prometheus-stack`
Chart and `environment` is one of dev, staging or prod.

The current `values.yml` of the deployed Chart can be retrieved with:

```bash
helm get values -n kube-prometheus-stack kube-prometheus-stack
```

### Uninstalling the helm Chart

You can uninstall the monitoring stack with

```bash
helm uninstall kube-prometheus-stack -n kube-prometheus-stack
kubectl delete ns kube-prometheus-stack
```

Additionally, the corresponding CRDs need to be deleted as documented in <https://github.com/prometheus-community/helm-charts/blob/main/charts/kube-prometheus-stack/README.md#uninstall-helm-chart>.
