{{/*
Expand the name of the chart.
*/}}
{{- define "api-platform.name" -}}
{{- default .Chart.Name .Values.chartNameOverride | trunc 63 | trimSuffix "-" }}
{{- end }}

{{/*
Create a default fully qualified app name.
If release name contains chart name it will be used as a full name.
*/}}
{{- define "api-platform.fullname" -}}
{{- $name := default .Chart.Name .Values.chartNameOverride }}
{{- if contains $name .Release.Name }}
{{- .Release.Name | trimSuffix "-" }}
{{- else }}
{{- printf "%s-%s" .Release.Name $name | trimSuffix "-" }}
{{- end }}
{{- end }}

{{/*
Name for all api-related resources.
We truncate at 63 chars because some Kubernetes name fields are limited to this (by the DNS naming spec).
*/}}
{{- define "api-platform.api-name" -}}
{{- $name := default .Chart.Name .Values.chartNameOverride }}
{{- if contains $name (include "api-platform.fullname" .) }}
{{- printf "%s-api" (include "api-platform.fullname" .) | trunc 63 | trimSuffix "-" }}
{{- else }}
{{- printf "%s-%s-api" (include "api-platform.fullname" .) $name | trunc 63 | trimSuffix "-" }}
{{- end }}
{{- end }}

{{/*
Create chart name and version as used by the chart label.
*/}}
{{- define "api-platform.chart" -}}
{{- printf "%s-%s" .Chart.Name .Chart.Version | replace "+" "_" | trunc 63 | trimSuffix "-" }}
{{- end }}

{{/*
Common labels
*/}}
{{- define "api-platform.labels" -}}
helm.sh/chart: {{ include "api-platform.chart" . }}
{{ include "api-platform.selectorLabels" . }}
{{- if .Chart.AppVersion }}
app.kubernetes.io/version: {{ .Chart.AppVersion | quote }}
{{- end }}
app.kubernetes.io/managed-by: {{ .Release.Service }}
{{- end }}

{{/*
Selector labels
*/}}
{{- define "api-platform.selectorLabels" -}}
app.kubernetes.io/name: {{ include "api-platform.name" . }}
app.kubernetes.io/instance: {{ .Release.Name }}
app.kubernetes.io/part-of: {{ include "api-platform.name" . }}
{{- end }}

{{/*
Create the name of the service account to use
*/}}
{{- define "api-platform.serviceAccountName" -}}
{{- if .Values.serviceAccount.create }}
{{- default (include "api-platform.fullname" .) .Values.serviceAccount.name }}
{{- else }}
{{- default "default" .Values.serviceAccount.name }}
{{- end }}
{{- end }}
