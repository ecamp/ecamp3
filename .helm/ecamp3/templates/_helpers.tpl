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
Name for all HTTP cache-related resources.
We truncate at 63 chars because some Kubernetes name fields are limited to this (by the DNS naming spec).
*/}}
{{- define "apiCache.name" -}}
{{- $name := default .Chart.Name .Values.chartNameOverride }}
{{- if contains $name (include "app.name" .) }}
{{- printf "%s-api-cache" (include "app.name" .) | trunc 63 | trimSuffix "-" }}
{{- else }}
{{- printf "%s-%s-api-cache" (include "app.name" .) $name | trunc 63 | trimSuffix "-" }}
{{- end }}
{{- end }}

{{/*
Name for all db_backup_job releated resources.
We truncate at 63 chars because some Kubernetes name fields are limited to this (by the DNS naming spec).
*/}}
{{- define "dbBackupJob.name" -}}
{{- $name := default .Chart.Name .Values.chartNameOverride }}
{{- if contains $name (include "app.name" .) }}
{{- printf "%s-db-backup-job" (include "app.name" .) | trunc 63 | trimSuffix "-" }}
{{- else }}
{{- printf "%s-%s-db-backup-job" (include "app.name" .) $name | trunc 63 | trimSuffix "-" }}
{{- end }}
{{- end }}

{{/*
Name for all hook_db_restore releated resources.
We truncate at 63 chars because some Kubernetes name fields are limited to this (by the DNS naming spec).
*/}}
{{- define "hookDbRestore.name" -}}
{{- $name := default .Chart.Name .Values.chartNameOverride }}
{{- if contains $name (include "app.name" .) }}
{{- printf "%s-hook-db-restore" (include "app.name" .) | trunc 63 | trimSuffix "-" }}
{{- else }}
{{- printf "%s-%s-hook-db-restore" (include "app.name" .) $name | trunc 63 | trimSuffix "-" }}
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
{{- printf "https://%s%s" .Values.domain .Values.api.subpath }}
{{- end }}

{{/*
The prefix used by API for setting cookie names
*/}}
{{- define "api.cookiePrefix" -}}
{{- printf "%s_" (.Values.domain | replace "." "_") }}
{{- end }}

{{/*
The full URL where the frontend will be available.
*/}}
{{- define "frontend.url" -}}
{{- printf "https://%s" .Values.domain }}
{{- end }}

{{/*
The full URL where the print service will be available.
*/}}
{{- define "print.url" -}}
{{- printf "https://%s%s" .Values.domain .Values.print.subpath }}
{{- end }}

{{/*
The full URL where the dummy mail catcher service will be available.
*/}}
{{- define "mail.url" -}}
{{- printf "https://%s%s" .Values.domain .Values.mail.subpath }}
{{- end }}

{{/*
Ingress basic auth secret name
*/}}
{{- define "ingress.basicAuth.secret.name" -}}
{{- printf "%s-basic-auth" (include "app.name" .) }}
{{- end }}

{{/*
Ingress annotations for basic auth
*/}}
{{- define "ingress.basicAuth.annotations" -}}
{{- if .Values.ingress.basicAuth.enabled }}
nginx.ingress.kubernetes.io/auth-type: "basic"
nginx.ingress.kubernetes.io/auth-secret: {{ include "ingress.basicAuth.secret.name" . | quote }}
nginx.ingress.kubernetes.io/auth-realm: {{ printf "Authentication Required - %s is protected and needs authentication" .Values.domain | quote }}
{{- end }}
{{- end }}

{{/*
Common labels
*/}}
{{- define "app.commonLabels" -}}
helm.sh/chart: {{ include "chart.fullname" . }}
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
app.kubernetes.io/part-of: {{ include "chart.name" . }}
{{- end }}

{{/*
Selector labels for API
*/}}
{{- define "api.selectorLabels" -}}
app.kubernetes.io/name: {{ include "chart.name" . }}-api
{{ include "app.commonSelectorLabels" . }}
{{- end }}

{{/*
Selector labels for Frontend
*/}}
{{- define "frontend.selectorLabels" -}}
app.kubernetes.io/name: {{ include "chart.name" . }}-frontend
{{ include "app.commonSelectorLabels" . }}
{{- end }}

{{/*
Selector labels for Print
*/}}
{{- define "print.selectorLabels" -}}
app.kubernetes.io/name: {{ include "chart.name" . }}-print
{{ include "app.commonSelectorLabels" . }}
{{- end }}

{{/*
Selector labels for Mail
*/}}
{{- define "mail.selectorLabels" -}}
app.kubernetes.io/name: {{ include "chart.name" . }}-mail
{{ include "app.commonSelectorLabels" . }}
{{- end }}

{{/*
Selector labels for Browserless
*/}}
{{- define "browserless.selectorLabels" -}}
app.kubernetes.io/name: {{ include "chart.name" . }}-browserless
{{ include "app.commonSelectorLabels" . }}
{{- end }}

{{/*
Selector labels for HTTP Cache
*/}}
{{- define "apiCache.selectorLabels" -}}
app.kubernetes.io/name: {{ include "chart.name" . }}-api-cache
{{ include "app.commonSelectorLabels" . }}
{{- end }}

{{/*
Selector labels for db-backup-job
*/}}
{{- define "dbBackupJob.selectorLabels" -}}
app.kubernetes.io/name: {{ include "chart.name" . }}-db-backup-job
{{ include "app.commonSelectorLabels" . }}
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
