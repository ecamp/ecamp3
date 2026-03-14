{{/*
Create a default fully qualified app name.
If release name contains chart name it will be used as a full name.
*/}}
{{- define "app.name" -}}
{{- if contains .Chart.Name .Release.Name }}
{{- .Release.Name | trimSuffix "-" }}
{{- else }}
{{- printf "%s-%s" .Release.Name (include "chart.name" .) | trimSuffix "-" }}
{{- end }}
{{- end }}

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
