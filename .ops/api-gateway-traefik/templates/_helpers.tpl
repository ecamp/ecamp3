{{/*
Expand the name of the chart.
*/}}
{{- define "app.name" -}}
{{- default .Chart.Name .Values.nameOverride | trunc 63 | trimSuffix "-" -}}
{{- end -}}

{{/*
Common labels
*/}}
{{- define "app.commonLabels" -}}
chart: {{ .Chart.Name }}
helm.sh/chart: {{ .Chart.Name }}
{{- if .Chart.AppVersion }}
app.kubernetes.io/version: {{ .Chart.AppVersion | quote }}
{{- end }}
app.kubernetes.io/managed-by: {{ .Release.Service }}
{{- end }}

{{/*
Common selector labels
*/}}
{{- define "app.commonSelectorLabels" -}}
app.kubernetes.io/instance: {{ .Release.Name }}
app.kubernetes.io/part-of: {{ .Chart.Name }}
chart: {{ .Chart.Name }}
{{- end }}

{{/*
Generate certificates for custom-metrics api server
*/}}
{{- define "app.gen-certs" -}}
{{- $altNames := list ( printf "%s.%s" (include "app.name" .) .Release.Namespace ) ( printf "%s.%s.svc" (include "app.name" .) .Release.Namespace ) -}}
{{- $ca := genCA "custom-metrics-ca" 9999 -}}
{{- $cert := genSignedCert ( include "app.name" . ) nil $altNames 9999 $ca -}}
tls.crt: {{ $cert.Cert | b64enc }}
tls.key: {{ $cert.Key | b64enc }}
{{- end -}}
