{{/*
Expand the name of the chart.
*/}}
{{- define "chart.name" -}}
{{- default .Chart.Name .Values.chartNameOverride }}
{{- end }}

{{/*
Create a default fully qualified app name.
If release name contains chart name it will be used as a full name.
*/}}
{{- define "app.name" -}}
{{- if contains (include "chart.name" .) .Release.Name }}
{{- .Release.Name | trimSuffix "-" }}
{{- else }}
{{- printf "%s-%s" .Release.Name (include "chart.name" .) | trimSuffix "-" }}
{{- end }}
{{- end }}

{{/*
Name for all api-related resources.
We truncate at 63 chars because some Kubernetes name fields are limited to this (by the DNS naming spec).
*/}}
{{- define "api.name" -}}
{{- $name := default .Chart.Name .Values.chartNameOverride }}
{{- if contains $name (include "app.name" .) }}
{{- printf "%s-api" (include "app.name" .) | trunc 63 | trimSuffix "-" }}
{{- else }}
{{- printf "%s-%s-api" (include "app.name" .) $name | trunc 63 | trimSuffix "-" }}
{{- end }}
{{- end }}

{{/*
Name for all frontend-related resources.
We truncate at 63 chars because some Kubernetes name fields are limited to this (by the DNS naming spec).
*/}}
{{- define "frontend.name" -}}
{{- $name := default .Chart.Name .Values.chartNameOverride }}
{{- if contains $name (include "app.name" .) }}
{{- printf "%s-frontend" (include "app.name" .) | trunc 63 | trimSuffix "-" }}
{{- else }}
{{- printf "%s-%s-frontend" (include "app.name" .) $name | trunc 63 | trimSuffix "-" }}
{{- end }}
{{- end }}

{{/*
Name for all nuxt-print-related resources.
We truncate at 63 chars because some Kubernetes name fields are limited to this (by the DNS naming spec).
*/}}
{{- define "print.name" -}}
{{- $name := default .Chart.Name .Values.chartNameOverride }}
{{- if contains $name (include "app.name" .) }}
{{- printf "%s-print" (include "app.name" .) | trunc 63 | trimSuffix "-" }}
{{- else }}
{{- printf "%s-%s-print" (include "app.name" .) $name | trunc 63 | trimSuffix "-" }}
{{- end }}
{{- end }}

{{/*
Name for all dummy-mailserver-related resources.
We truncate at 63 chars because some Kubernetes name fields are limited to this (by the DNS naming spec).
*/}}
{{- define "mail.name" -}}
{{- $name := default .Chart.Name .Values.chartNameOverride }}
{{- if contains $name (include "app.name" .) }}
{{- printf "%s-mail" (include "app.name" .) | trunc 63 | trimSuffix "-" }}
{{- else }}
{{- printf "%s-%s-mail" (include "app.name" .) $name | trunc 63 | trimSuffix "-" }}
{{- end }}
{{- end }}

{{/*
Name for all browserless-related resources.
We truncate at 63 chars because some Kubernetes name fields are limited to this (by the DNS naming spec).
*/}}
{{- define "browserless.name" -}}
{{- $name := default .Chart.Name .Values.chartNameOverride }}
{{- if contains $name (include "app.name" .) }}
{{- printf "%s-browserless" (include "app.name" .) | trunc 63 | trimSuffix "-" }}
{{- else }}
{{- printf "%s-%s-browserless" (include "app.name" .) $name | trunc 63 | trimSuffix "-" }}
{{- end }}
{{- end }}

{{/*
Create chart name and version as used by the chart label.
*/}}
{{- define "chart.fullname" -}}
{{- printf "%s-%s" .Chart.Name .Chart.Version | replace "+" "_" | trunc 63 | trimSuffix "-" }}
{{- end }}

{{/*
The full URL where the API root will be available.
*/}}
{{- define "api.url" -}}
{{- printf "https://%s" .Values.api.domain }}
{{- end }}

{{/*
The full URL where the frontend will be available.
*/}}
{{- define "frontend.url" -}}
{{- printf "https://%s" .Values.frontend.domain }}
{{- end }}

{{/*
The full URL where the print service will be available.
*/}}
{{- define "print.url" -}}
{{- printf "https://%s" .Values.print.domain }}
{{- end }}

{{/*
The full URL where the dummy mail catcher service will be available.
*/}}
{{- define "mail.url" -}}
{{- printf "https://%s" .Values.mail.domain }}
{{- end }}

{{/*
Common labels
*/}}
{{- define "app.labels" -}}
helm.sh/chart: {{ include "chart.fullname" . }}
{{ include "app.selectorLabels" . }}
{{- if .Chart.AppVersion }}
app.kubernetes.io/version: {{ .Chart.AppVersion | quote }}
{{- end }}
app.kubernetes.io/managed-by: {{ .Release.Service }}
{{- end }}

{{/*
Selector labels
*/}}
{{- define "app.selectorLabels" -}}
app.kubernetes.io/name: {{ include "chart.name" . }}
app.kubernetes.io/instance: {{ .Release.Name }}
app.kubernetes.io/part-of: {{ include "chart.name" . }}
{{- end }}

{{/*
Create the name of the service account to use
*/}}
{{- define "app.serviceAccountName" -}}
{{- if .Values.serviceAccount.create }}
{{- default (include "app.name" .) .Values.serviceAccount.name }}
{{- else }}
{{- default "default" .Values.serviceAccount.name }}
{{- end }}
{{- end }}
