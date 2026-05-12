<template>
  <v-toolbar-items v-if="period.camp().periods().items.length > 1">
    <v-menu offset-y>
      <template #activator="{ props, isActive }">
        <v-btn
          variant="text"
          size="large"
          class="justify-start px-4 my-1 rounded-pill ec-period-switcher__button"
          height="auto"
          block
          v-bind="props"
        >
          <h1 class="text-subtitle-1">
            {{ period.description }}
          </h1>
          <v-icon v-if="isActive" end>mdi-menu-up</v-icon>
          <v-icon v-else end>mdi-menu-down</v-icon>
        </v-btn>
      </template>
      <v-list>
        <v-list-subheader>{{
          $t('components.program.periodSwitcher.title')
        }}</v-list-subheader>
        <v-list-item
          v-for="item in allPeriods"
          :key="item._meta.self"
          :to="periodRoute(item, routeName)"
          lines="two"
        >
          <v-list-item-title>{{ item.description }}</v-list-item-title>
          <v-list-item-subtitle>
            {{ dateRange(item.start, item.end) }}
          </v-list-item-subtitle>
        </v-list-item>
      </v-list>
    </v-menu>
  </v-toolbar-items>
  <v-toolbar-title v-else>
    <h1 class="text-subtitle-1 text-center">
      {{ period.description }}
    </h1>
  </v-toolbar-title>
</template>
<script>
import { sortBy } from 'lodash-es'
import { periodRoute } from '@/router.js'
import { dateHelperUTCFormatted } from '@/mixins/dateHelperUTCFormatted.js'

export default {
  name: 'PeriodSwitcher',
  mixins: [dateHelperUTCFormatted],
  props: {
    period: {
      type: Object,
      required: true,
    },
    routeName: { type: String, default: 'camp/period/program' },
  },
  computed: {
    allPeriods() {
      return sortBy(this.period.camp().periods().items, (p) => p.start)
    },
  },
  methods: {
    periodRoute,
  },
}
</script>
<style lang="scss" scoped>
.ec-period-switcher__button {
  height: calc(100% - 8px) !important;
}
</style>
