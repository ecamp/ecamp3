<template>
  <v-toolbar-items v-if="period().camp().periods().items.length > 1">
    <v-menu offset-y>
      <template #activator="{ on, attrs, value }">
        <v-btn
          text large
          class="justify-start px-2"
          height="auto" block
          v-bind="attrs"
          v-on="on">
          <h1 class="text-subtitle-1">
            {{ period().description }}
          </h1>
          <v-icon v-if="value" right>mdi-menu-up</v-icon>
          <v-icon v-else right>mdi-menu-down</v-icon>
        </v-btn>
      </template>
      <v-list>
        <v-subheader>{{ $tc('components.camp.periodSwitcher.title') }}</v-subheader>
        <v-list-item v-for="item in period().camp().periods().items" :key="item._meta.self"
                     :to="periodRoute(item)"
                     two-line>
          <v-list-item-content>
            <v-list-item-title>{{ item.description }}</v-list-item-title>
            <v-list-item-subtitle>
              {{ dateRange(item.start, item.end) }}
            </v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-menu>
  </v-toolbar-items>
  <v-toolbar-title v-else>
    <h1 class="text-h6">
      {{ period().description }}
    </h1>
  </v-toolbar-title>
</template>
<script>
import { periodRoute } from '@/router.js'
import { dateRange } from '@/common/helpers/dateHelperUTCFormatted.js'

export default {
  name: 'PeriodSwitcher',
  props: {
    period: {
      type: Function,
      required: true
    }
  },
  methods: {
    periodRoute,
    dateRange
  }
}
</script>
<style lang="scss" scoped>

</style>
