<template>
  <div>
    <div>
      <generic-error-message v-if="error" :error="error" />
      <div v-for="(content, idx) in config.contents" v-else :key="idx">
        <component
          :is="'Config' + content.type"
          :options="content.options"
          :camp="camp"
          :config="config"
          :page-size="pageSize"
          :index="idx"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { toDayjsLocale } from '@/common/helpers/dayjs.js'
import toLower from 'lodash-es/toLower.js'

// parse query config
const route = useRoute()
const query = route.query
const config = JSON.parse(query.config || '{}')

// set locale
const { setLocale, fallbackLocale } = useI18n()
const { $date } = useNuxtApp()
const rawFallback = fallbackLocale.value
const fallback =
  typeof rawFallback === 'string'
    ? rawFallback
    : Array.isArray(rawFallback)
      ? rawFallback[0] || 'en'
      : rawFallback?.default?.[0] || (rawFallback && Object.values(rawFallback)[0]?.[0]) || 'en'
const locale = config.language || fallback
await setLocale(locale) // i18n

// page size
const pageSize = toLower(config.options?.pageSize ?? 'A4')

//dayjs
$date.locale(toDayjsLocale(locale))

// load camp
const { $api } = useNuxtApp()
const { data: camp, error } = await useAsyncData(
  'camp',
  () => $api.get(config.camp)._meta.load
)
</script>

<!-- these styles should be global, thus we don't want the vue-scoped-css/enforce-style-type warning here -->
<!-- eslint-disable-next-line -->
<style>
:root {
  --tw-prose-body: #000 !important;
}

body {
  color: #000;
  font-family:
    'InterDisplay',
    Helvetica Neue,
    Helvetica,
    Arial,
    sans-serif;
  font-size: 10pt;
}

.tw-prose ol {
  padding-left: 16px;
}

.tw-prose li ::marker {
  color: #000;
}
</style>
