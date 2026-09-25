#!/bin/bash
# Install a kibana-objects.ndjson into a running Elasticsearch/Kibana pair and
# check it against the four log fixtures of bacluc-agent/agent-todo#270 step 6.
#
#   MODE=post NDJSON=<file> verify-kibana-objects.sh   assert the fixed config
#   MODE=pre  NDJSON=<file> verify-kibana-objects.sh   assert the config is still broken
#
# Needs curl and jq, and Elasticsearch and Kibana reachable (the workflow starts
# both on localhost:9200 and localhost:5601).
set -uo pipefail

MODE=${MODE:-post}
case "$MODE" in
  post | pre) ;;
  *) echo "MODE must be 'post' or 'pre', got '$MODE'" >&2; exit 2 ;;
esac

KIBANA_HOST=${KIBANA_HOST:-http://localhost:5601}
ES_HOST=${ES_HOST:-http://localhost:9200}
SCRIPT_DIR=$(realpath "$(dirname "$0")")
NDJSON=${NDJSON:-$SCRIPT_DIR/kibana-objects.ndjson}
INDEX=logstash-ci

DATA_VIEW=e270616c-823f-485b-b1e5-0d3435383b91
SEARCH=02e434c0-01a0-11ef-84f3-475f3a574907
REFERER=b36bef4d-5320-444b-8cd4-c595b2890491
DASHBOARD=cbf725c0-705f-11ee-bdbe-0de3df9703e1
CACHE_PANEL=4bdb9d4e-d353-413e-9013-064e76c0116d

fail=0
check() { # <what> <expected> <actual>
  case "$3" in
    ERROR:*) echo "ERROR $1: $3" >&2; exit 1 ;;
  esac
  if [ "$2" = "$3" ]; then
    printf 'ok    %-62s %s\n' "$1" "$3"
  else
    printf 'FAIL  %-62s expected %s, got %s\n' "$1" "$2" "$3"
    fail=$((fail + 1))
  fi
}
note() { printf '\n== %s\n' "$1"; }

# Elasticsearch. Kibana's /api/console/proxy refuses DELETE, so the fixture index
# lifecycle cannot go through it.
es() { # <path> <method> [body]; Elasticsearch rejects a body on DELETE
  if [ $# -gt 2 ]; then
    curl -sS -X "$2" "$ES_HOST/$1" -H 'Content-Type: application/json' --data-binary @- <<<"$3"
  else
    curl -sS -X "$2" "$ES_HOST/$1"
  fi
}
# .aggregations of a search body against one index, or "ERROR: <reason>" when ES
# refused it.
aggs_on() { es "$1/_search" POST "$2" |
  jq -c 'if .error then "ERROR: " + (.error.failed_shards[0].reason.caused_by.reason // .error.reason)
         else .aggregations end'; }
aggs() { aggs_on 'logstash-*' "$1"; }
# total hit count of a search body, or "ERROR: <reason>".
hits() { es 'logstash-*/_search' POST "$1" |
  jq -r 'if .error then "ERROR: " + (.error.failed_shards[0].reason.caused_by.reason // .error.reason)
         else (.hits.total.value | tostring) end'; }
# field_caps lookup for one field. An ES error has no .fields, so report it the
# way aggs/hits do; check() then aborts, instead of pre mode reading a broken
# lookup as "the field is absent" and proving a bug Elasticsearch never reported.
caps() { es 'logstash-*/_field_caps' POST "$(jq -nc --arg f "$1" '{fields: [$f]}')" |
  jq -r --arg f "$1" 'if .fields then (.fields | has($f) | tostring)
                        else "ERROR: " + ((.error.reason // "no .fields in response") | tostring) end'; }
# Hit count for a KQL string exactly as the saved object stores it. Used only for
# the dashboard-wide, saved-search and cache-column queries, which the issue
# prescribes as literal strings; the numeric assertions above use explicit
# aggregation DSL. The runtime mappings travel with every query, otherwise a
# KQL that names a runtime field resolves to nothing.
kql_hits() { hits "$(jq -nc --arg q "$1" --argjson rm "$runtime" \
  '{"size": 0, "runtime_mappings": $rm, "query": {"query_string": {"query": $q}}}')"; }
saved() { curl -sS "$KIBANA_HOST/api/saved_objects/$1/$2" -H 'kbn-xsrf: true'; }
search_query() { saved search "$1" | jq -r '.attributes.kibanaSavedObjectMeta.searchSourceJSON | fromjson | .query.query'; }
# search_query's "" is also what a missing or unreadable object yields, so report
# the query inside a marker that only a real search can produce.
marked_query() { saved search "$1" | jq -r 'if .attributes
  then "query=<" + (.attributes.kibanaSavedObjectMeta.searchSourceJSON | fromjson | .query.query) + ">"
  else "ERROR: no search saved object" end'; }

note "install $NDJSON"
# Same import call as restore-kibana-objects.sh, but that script reads the ndjson
# next to itself, so it cannot be reused here without changing it.
# Kibana rejects the upload unless the multipart filename ends in .ndjson.
tmp=/tmp/$(uuidgen).ndjson
trap 'rm -f "$tmp"' EXIT
jq -c . <"$NDJSON" >"$tmp"
import=$(curl -sS -X POST "$KIBANA_HOST/api/saved_objects/_import?createNewCopies=false&overwrite=true" \
  -H 'kbn-xsrf: true' --form "file=@$tmp")
check 'import success' 'true' "$(jq -r '.success' <<<"$import")"
check 'import successCount' "$(jq -s length <"$NDJSON")" "$(jq -r '.successCount' <<<"$import")"
check 'import errors' 'null' "$(jq -c '.errors' <<<"$import")"
check 'import warnings' '[]' "$(jq -c '.warnings' <<<"$import")"

note "index the four step-6 fixtures (method, status, X-Cache, duration)"
# Recreate the fixture index rather than letting _bulk auto-create it, so a rerun
# starts from a known mapping.
es "$INDEX" DELETE >/dev/null
check 'fixture index created' 'true' \
  "$(es "$INDEX" PUT '{"mappings":{"subobjects":false}}' | jq -r '.acknowledged // false')"
# subobjects:false, so the label is written as a literal dotted key the way the
# normalized fluentd output carries it, not as a nested object.
bulk=$(jq -cn '
  [ { "id": "one",   "method": "GET",   "status": 200, "cache": "HIT",     "duration": 1000000000 },
    { "id": "two",   "method": "GET",   "status": 404, "cache": "MISS",    "duration": 2000000000 },
    { "id": "three", "method": "PATCH", "status": 500, "cache": "HITMISS", "duration": 3000000000 },
    { "id": "four",  "method": "GET",   "status": 200, "cache": "PASS",    "duration": 4000000000 } ]
  | .[] | . as $f | ("/fixtures/" + $f.id) as $url
  | ( { "index": { "_index": "'"$INDEX"'", "_id": $f.id } },
      { "@timestamp": "2026-01-15T10:00:00.000Z",
        "method": $f.method, "status": $f.status, "duration": $f.duration,
        "path": $url, "escapedUrl": $url, "escapedUrlWithoutQuery": $url,
        "log": ($f.method + " " + $url + " " + ($f.status | tostring)),
        "json": { "RequestHost": "app.ecamp3.ch",
                  "request_Referer": "https://app.ecamp3.ch/",
                  "entryPointName": "web",
                  "downstream_X-Cache": $f.cache },
        "kubernetes.labels.app.kubernetes.io/instance": "ecamp3-api-gateway",
        "kubernetes.pod_name": "api-gateway-0",
        "kubernetes.namespace_name": "api-gateway" } )
  | tostring' | jq -rsc 'join("\n") + "\n"')
check 'bulk request accepted every fixture' 'false' \
  "$(es "$INDEX/_bulk" POST "$bulk" | jq -r '.errors | tostring')"
es "$INDEX/_refresh" POST >/dev/null
check 'fixtures indexed' '4' "$(hits '{"size": 0}')"

# The runtime-field scripts under test are read back from the data view Kibana
# is now serving, so a script that Kibana stored but Elasticsearch cannot
# compile shows up as "ERROR: ..." in every aggregation below.
runtime=$(saved index-pattern "$DATA_VIEW" | jq -c '.attributes.runtimeFieldMap | fromjson')
with_runtime() { jq -c --argjson rm "$runtime" '. + {runtime_mappings: $rm}' <<<"$1"; }

# Every field the dashboard and the two saved searches reference: the Lens column
# sourceFields, the options-list control field and the search columns. The KQL
# filters are covered as well, because they are executed as aggregations below.
panels_fields=$(jq -n -c \
  --argjson dash "$(saved dashboard "$DASHBOARD" | jq -c '
    [.attributes.panelsJSON | fromjson | .[]
     | (.embeddableConfig.attributes.state.datasourceStates.formBased.layers // {}) | to_entries[].value.columns
     | to_entries[].value.sourceField] + [.attributes.controlGroupInput.panelsJSON | fromjson
     | to_entries[].value.explicitInput.fieldName]')" \
  --argjson s1 "$(saved search "$SEARCH" | jq -c '.attributes.columns')" \
  --argjson s2 "$(saved search "$REFERER" | jq -c '.attributes.columns')" \
  '($dash + $s1 + $s2) | map(select(. != null)) | unique')

if [ "$MODE" = pre ]; then
  note "PRE-FIX: this config is expected to be broken"
  for legacy in json.httpRequest.status json.httpRequest.request_time_seconds; do
    check "$legacy is missing from the index (the bug)" 'false' "$(caps "$legacy")"
    check "a panel still references $legacy (the bug)" 'true' \
      "$(jq -r --arg f "$legacy" 'any(. == $f) | tostring' <<<"$panels_fields")"
  done
  check 'no runtime fields are declared (the bug)' 'false' "$(jq -r '. != {} | tostring' <<<"$runtime")"
  check 'stale fieldAttrs still mention requestUrl (the bug)' 'true' \
    "$(saved index-pattern "$DATA_VIEW" | jq -r '.attributes.fieldAttrs | contains("requestUrl") | tostring')"
  check 'the dashboard-wide query matches no fixture (the bug)' '0' \
    "$(kql_hits "$(saved dashboard "$DASHBOARD" | jq -r '.attributes.kibanaSavedObjectMeta.searchSourceJSON | fromjson | .query.query')")"
  note "PRE-FIX numbers, printed for the record"
  echo "     saved objects:      $(jq -r -s '[ .[] | "\(.type)/\(.id)" ] | join(" ")' <"$NDJSON")"
  echo "     saved searches:     $(jq -r -s '[ .[] | select(.type == "search") ] | length' <"$NDJSON")"
  echo "     runtime fields:     $(jq -c keys <<<"$runtime")"
  echo "     panel fields:       $(jq -r '.[]' <<<"$panels_fields" | tr '\n' ' ')"
  echo "     dashboard query:    $(saved dashboard "$DASHBOARD" | jq -r '.attributes.kibanaSavedObjectMeta.searchSourceJSON | fromjson | .query.query')"
  echo "     fixtures indexed:   $(hits '{"size": 0}') of 4"
  echo "     Follow referer is not part of the pre-fix config, its check is N/A here"
  printf '\n----------------------------------------\n'
  if [ "$fail" -eq 0 ]; then
    echo 'the pre-fix config is still broken as expected'
    exit 0
  fi
  echo "$fail check(s) failed: the pre-fix baseline no longer shows the bug"
  exit 1
fi

note "data view $DATA_VIEW as Kibana serves it"
jq -r 'to_entries[] | "     \(.key) (\(.value.type)): \(.value.script)"' <<<"$runtime"
check 'title' '"logstash-*"' "$(saved index-pattern "$DATA_VIEW" | jq -c '.attributes.title')"
check 'timeFieldName' '"@timestamp"' "$(saved index-pattern "$DATA_VIEW" | jq -c '.attributes.timeFieldName')"
check 'fieldAttrs is reset' '"{}"' "$(saved index-pattern "$DATA_VIEW" | jq -c '.attributes.fieldAttrs')"
check 'durationSeconds type' '"double"' "$(jq -c '.durationSeconds.type' <<<"$runtime")"
check 'cacheStatus type' '"keyword"' "$(jq -c '.cacheStatus.type' <<<"$runtime")"

note 'step 6, first confirmation: no field a panel or search references is missing'
check 'the dashboard and both searches were read' 'true' "$(jq -r 'length > 5' <<<"$panels_fields")"
for f in $(jq -r '.[]' <<<"$panels_fields"); do
  case "$f" in
    ___records___) continue ;;
    durationSeconds | cacheStatus)
      check "$f is a declared runtime field" 'true' "$(jq -r --arg f "$f" 'has($f) | tostring' <<<"$runtime")" ;;
    *) check "$f exists in the index" 'true' "$(caps "$f")" ;;
  esac
done
# The one expected value not taken from issue steps 2-4: it pins that the fix
# edited panels in place instead of dropping any, and 7 is not the issue's full
# intent. See the missing static five-second reference line in the PR description.
check 'dashboard panel count' '7' "$(saved dashboard "$DASHBOARD" | jq -r '.attributes.panelsJSON | fromjson | length')"

note 'step 6, second confirmation: durationSeconds reports 1, 2, 3 and 4'
dur=$(aggs "$(with_runtime '{"size": 0, "aggs": {"d": {"stats": {"field": "durationSeconds"}}}}')")
secs=$(es 'logstash-*/_search' POST "$(with_runtime '{"size": 4, "_source": false,
  "sort": [{"durationSeconds": {"order": "asc"}}], "fields": ["durationSeconds"]}')" |
  jq -c '[.hits.hits[].fields.durationSeconds[]] | map(floor)')
echo "     stats: $dur"
echo "     per document: $secs"
check 'the durationSeconds aggregation runs' 'true' "$(jq -r 'type == "object"' <<<"$dur")"
check 'durationSeconds count' '4' "$(jq -r '.d.count // 0' <<<"$dur")"
check 'durationSeconds values' '[1,2,3,4]' "$secs"

note 'step 6, third confirmation: two 200, one 400, one 500, three GET and one mutating'
# KQL `method : "GET"` on the analysed method field is a match_phrase.
cls=$(aggs "$(with_runtime '{"size": 0, "aggs": {"c": {"filters": {"filters": {
  "200": {"range": {"status": {"gte": 200, "lt": 300}}},
  "400": {"range": {"status": {"gte": 400, "lt": 500}}},
  "500": {"range": {"status": {"gte": 500}}} }}}}}')")
met=$(aggs "$(with_runtime '{"size": 0, "aggs": {"m": {"filters": {"filters": {
  "get": {"match_phrase": {"method": "GET"}},
  "mutating": {"bool": {"should": [
    {"match_phrase": {"method": "POST"}},
    {"match_phrase": {"method": "PATCH"}},
    {"match_phrase": {"method": "DELETE"}}], "minimum_should_match": 1}} } } }}}')")
echo "     response classes: $cls"
echo "     methods:          $met"
check 'the response-class aggregation runs' 'true' "$(jq -r 'type == "object"' <<<"$cls")"
check 'the method aggregation runs' 'true' "$(jq -r 'type == "object"' <<<"$met")"
check '200 responses' '2' "$(jq -r '.c.buckets["200"].doc_count // 0' <<<"$cls")"
check '400 responses' '1' "$(jq -r '.c.buckets["400"].doc_count // 0' <<<"$cls")"
check '500 responses' '1' "$(jq -r '.c.buckets["500"].doc_count // 0' <<<"$cls")"
check 'GET requests' '3' "$(jq -r '.m.buckets.get.doc_count // 0' <<<"$met")"
check 'mutating requests' '1' "$(jq -r '.m.buckets.mutating.doc_count // 0' <<<"$met")"
# The ranges above are this script's own DSL; assert too that the response-class panel
# stores exactly the three filters the issue prescribes, each of which is what a viewer
# of the dashboard actually runs.
check 'the response-class filters' '["status >= 200 and status < 300","status >= 400 AND status < 500","status >= 500"]' \
  "$(saved dashboard "$DASHBOARD" | jq -c '
      [.attributes.panelsJSON | fromjson | .[]
       | (.embeddableConfig.attributes.state.datasourceStates.formBased.layers // {})
       | to_entries[].value.columns | to_entries[].value
       | select(.filter != null) | .filter.query
       | select(startswith("status"))] | sort')"

note 'step 6, fourth confirmation: one of each cache outcome and 66.67% HIT'
# Read the cache table's columns and its formula back out of the installed
# dashboard rather than hardcoding them. KQL `cacheStatus : "X"` is a term query
# because cacheStatus is a runtime keyword field.
panel=$(saved dashboard "$DASHBOARD" | jq -c --arg p "$CACHE_PANEL" '
  .attributes.panelsJSON | fromjson | .[] | select(.panelIndex == $p)
  | .embeddableConfig.attributes.state')
cols=$(jq -c '.datasourceStates.formBased.layers | to_entries[].value.columns' <<<"$panel")
echo "     layer filter: $(jq -r '.datasourceStates.formBased.layers | to_entries[].value.filter.query' <<<"$panel")"
echo "     columns:      $(jq -r 'keys | join(" ")' <<<"$cols")"
# The issue prescribes a HIT % formula column. Kibana 8.13 cannot render it: the
# panel crashed with "Cannot read properties of undefined (reading 'columns')"
# in getVisualizationInfo and issued no query at all, and no formula shape
# (references, math companion, column-variable operands) avoided either that
# crash, a "does not accept any field" error, or a silently all-null column.
# The column is therefore gone and both references point at the HIT count; the
# loss is the open question in the PR description. What is left to assert is
# that nothing formula-shaped survives, because that is what used to crash it.
check 'no formula column remains' '0' \
  "$(jq -r '[to_entries[] | select(.value.isFormula == true)] | length' <<<"$cols")"
check 'the table sorts by the HIT count' 'cache_hit_col' \
  "$(jq -r '.visualization.sorting.columnId' <<<"$panel")"
check 'the terms column orders by the HIT count' 'cache_hit_col' \
  "$(jq -r '.datasourceStates.formBased.layers | to_entries[].value.columns["6a02ef93-f314-42f2-bca1-65af849ad659"].params.orderBy.columnId' <<<"$panel")"
check 'every remaining column resolves in the layer' 'true' \
  "$(jq -r '(.datasourceStates.formBased.layers | to_entries[].value) as $l
      | [$l.columnOrder[], (.visualization.columns[].columnId)]
      | (unique | all(. as $c | $l.columns | has($c))) | tostring' <<<"$panel")"
# The table's own filter and its four count columns, each executed with the KQL the
# dashboard stores, so a cache table that counts something else than the four
# uppercase outcomes fails instead of being re-derived here.
check 'the cache table filter' 'cacheStatus : * AND NOT escapedUrlWithoutQuery : "^/auth/"' \
  "$(jq -r '.datasourceStates.formBased.layers | to_entries[].value.filter.query' <<<"$panel")"
for pair in HIT:cache_hit_col HITMISS:cache_hitmiss_col MISS:cache_miss_col PASS:cache_pass_col; do
  outcome=${pair%%:*}
  column=${pair##*:}
  check "the $outcome column of the cache table matches one fixture" '1' \
    "$(kql_hits "$(jq -r --arg c "$column" '.[$c].filter.query' <<<"$cols")")"
done
cache=$(aggs "$(with_runtime '{"size": 0, "query": {"bool": {"filter": [
  {"exists": {"field": "cacheStatus"}},
  {"bool": {"must_not": {"match_phrase": {"escapedUrlWithoutQuery": "^/auth/"}}}}]}},
  "aggs": {"c": {"terms": {"field": "cacheStatus"}}}}')")
echo "     $cache"
check 'the cache aggregation runs' 'true' "$(jq -r 'type == "object"' <<<"$cache")"
check 'cacheStatus values' '{"HIT":1,"HITMISS":1,"MISS":1,"PASS":1}' \
  "$(jq -c '.c.buckets | map({key, value: .doc_count}) | from_entries' <<<"$cache")"
# The share the removed formula column rendered. Kept as the arithmetic the
# buckets have to keep producing, so a fixture change that silently moves the
# hit ratio still fails.
check 'HIT %' '66.67' \
  "$(jq -nr --argjson c "$cache" '($c.c.buckets | map({key, value: .doc_count}) | from_entries) as $b
      | ((($b.HIT + $b.HITMISS) / ($b.HIT + $b.HITMISS + $b.MISS) * 10000) | round) / 100')"

note 'step 6, fifth confirmation: both saved searches return their matching records'
# The issue prescribes which columns each search keeps, so assert the installed set
# rather than only that every column it happens to have resolves.
check "search $SEARCH columns" \
  '["durationSeconds","escapedUrl","escapedUrlWithoutQuery","kubernetes.labels.app.kubernetes.io/instance","kubernetes.pod_name","log","path"]' \
  "$(saved search "$SEARCH" | jq -c '.attributes.columns | sort')"
check "search $SEARCH has no query" 'query=<>' "$(marked_query "$SEARCH")"
check "search $SEARCH matches every fixture" '4' "$(hits '{"size": 0, "query": {"match_all": {}}}')"
check "search $REFERER exists" '200' \
  "$(curl -sS -o /dev/null -w '%{http_code}' "$KIBANA_HOST/api/saved_objects/search/$REFERER" -H 'kbn-xsrf: true')"
check "search $REFERER columns" \
  '["escapedUrl","escapedUrlWithoutQuery","kubernetes.labels.app.kubernetes.io/instance","kubernetes.pod_name","log","path","status"]' \
  "$(saved search "$REFERER" | jq -c '.attributes.columns | sort')"
referer_query=$(search_query "$REFERER")
echo "     query: $referer_query"
check "search $REFERER matches every fixture" '4' "$(kql_hits "$referer_query")"

note 'step 4 dashboard-wide query selects the fixtures'
dash_query=$(saved dashboard "$DASHBOARD" | jq -r '.attributes.kibanaSavedObjectMeta.searchSourceJSON | fromjson | .query.query')
echo "     query: $dash_query"
check 'dashboard-wide query matches every fixture' '4' "$(kql_hits "$dash_query")"

note 'KNOWN LIMITATION: the two runtime-field scripts are not null-safe'
# doc.containsKey() is segment scoped, so a single document in the segment without
# the field makes the aggregation throw. Shown on a throwaway index that is
# created after every counted assertion above, and named so that it cannot match
# the logstash-* data view even if this run dies before the cleanup below, so the
# fixtures keep their counts. This is the evidence for the open question in the PR
# description; it is informational and never fails the run.
DEMO=kibana-verify-null-safety-demo
es "$DEMO" DELETE >/dev/null
es "$DEMO" PUT '{"mappings":{"subobjects":false}}' >/dev/null
# One bulk request, so both documents land in the same segment.
es "$DEMO/_bulk" POST '{"index":{"_id":"with-duration"}}
{"@timestamp":"2026-01-15T10:00:00.000Z","duration":1000000000,"json":{"downstream_X-Cache":"HIT"}}
{"index":{"_id":"without-duration"}}
{"@timestamp":"2026-01-15T10:00:01.000Z","json":{"RequestHost":"app.ecamp3.ch"}}
' >/dev/null
es "$DEMO/_refresh" POST >/dev/null
demo=$(es "$DEMO/_search" POST "$(with_runtime '{"size": 0, "aggs": {
  "durationSeconds": {"stats": {"field": "durationSeconds"}},
  "cacheStatus": {"terms": {"field": "cacheStatus"}}}}')")
printf '     %s\n' "$(jq -c 'if .error then (.error.failed_shards[0].reason | {type, reason, script})
   else .aggregations end' <<<"$demo")"
es "$DEMO" DELETE >/dev/null

printf '\n----------------------------------------\n'
if [ "$fail" -eq 0 ]; then
  echo 'all checks passed'
  exit 0
fi
echo "$fail check(s) failed"
exit 1
