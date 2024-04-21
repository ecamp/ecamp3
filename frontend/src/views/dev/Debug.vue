<template>
  <v-container fluid class="d-grid h-full">
    <content-card title="Debug" toolbar class="ma-sm-auto" style="width: auto">
      <template #title>
        <Coffee />
        <v-toolbar-title tag="h1" class="font-weight-bold ml-2">Debug</v-toolbar-title>
      </template>
      <template #title-actions>
        <language-switcher v-if="isDev" />
      </template>
      <v-card-text>
        <h3>
          Version
          <a v-if="version" :href="versionLink" target="_blank">
            {{ version }}
          </a>
          <span class="ml-1">{{ deploymentTime }}</span>
        </h3>
      </v-card-text>
      <v-simple-table dense>
        <tbody>
          <tr v-for="[key, value] in envArray" :key="key">
            <th>{{ key }}</th>
            <td>
              <code v-if="value !== undefined && value !== ''">{{ value }}</code>
            </td>
          </tr>
        </tbody>
      </v-simple-table>
    </content-card>
  </v-container>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import { getEnv } from '@/environment.js'
import Coffee from '@/assets/icons/Coffee.vue'
import LanguageSwitcher from '@/components/layout/LanguageSwitcher.vue'
import { parseTemplate } from 'url-template'

export default {
  name: 'Debug',
  components: {
    LanguageSwitcher,
    Coffee,
    ContentCard,
  },
  data: function () {
    return {
      environment: getEnv(),
    }
  },
  computed: {
    envArray() {
      return Object.entries(this.environment)
    },
    isDev() {
      return this.environment.FEATURE_DEVELOPER ?? false
    },
    deploymentTime() {
      const timestamp = this.environment.DEPLOYMENT_TIME
      const dateTime = timestamp ? this.$date.unix(timestamp) : this.$date()
      return dateTime.format(this.$tc('global.datetime.dateTimeLong'))
    },
    version() {
      return this.environment.VERSION || ''
    },
    versionLink() {
      return (
        parseTemplate(this.environment.VERSION_LINK_TEMPLATE).expand({
          version: this.version,
        }) || '#'
      )
    },
  },
}
</script>

<style scoped lang="scss">
@media #{map-get($display-breakpoints, 'xs-only')} {
  :deep(table) {
    display: block;
  }
  tbody {
    display: block;
  }
  tr {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    justify-content: space-between;
    border-bottom: thin solid rgba(0, 0, 0, 0.12);
    padding: 8px 2px;
  }
  th,
  td {
    user-select: initial;
    border: none !important;
    display: flex;
    align-items: center;
    height: auto !important;
    max-width: 100%;
  }
  code {
    display: block;
    width: 100%;
  }
}
</style>
