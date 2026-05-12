<template>
  <v-list-item lines="two" :to="campRoute(camp)">
    <v-list-item-title class="d-flex gap-x-2 justify-space-between">
      <strong class="whitespace-normal">{{ camp.title }}</strong>
      <v-chip
        v-if="camp.isShared === true"
        size="x-small"
        class="align-self-center px-1 v-btn--has-bg"
        >{{ $t('components.camp.campListItem.public') }}</v-chip
      >
      <span class="ec-camp-list-item-date whitespace-normal max-w-60vw text-right">
        <template v-for="(group, i) in date" :key="i">
          <template v-if="!!i"> | </template>
          <span class="break-inside-avoid">{{ group }}</span>
        </template>
      </span>
    </v-list-item-title>
    <v-list-item-subtitle class="d-flex gap-2 flex-wrap-reverse justify-space-between">
      <span class="whitespace-normal">{{ camp.motto }}</span>
      <span>{{ camp.organizer }}</span>
    </v-list-item-subtitle>
  </v-list-item>
</template>

<script>
import { campRoute } from '@/router.js'
import { componentI18n } from '@/plugins/i18n/index.js'
import { groupBy, uniq } from 'lodash-es'

const YEAR_JOINER = '-'

export default {
  name: 'CampListItem',
  props: {
    camp: { type: Object, required: true },
    periods: { type: Array, default: () => [] },
  },
  computed: {
    formatMY() {
      return new Intl.DateTimeFormat(componentI18n.locale, {
        year: 'numeric',
        month: 'short',
      })
    },
    formatM() {
      return new Intl.DateTimeFormat(componentI18n.locale, { month: 'short' })
    },
    date() {
      return Object.entries(this.groupedPeriods).map(([key, periods]) => {
        if (key.includes(YEAR_JOINER))
          return periods
            .slice(0, 1)
            .map(({ start, end }) => this.formatMY.formatRange(start, end))[0]
        else
          return (
            uniq(
              periods.map(({ start, end }) => this.formatM.formatRange(start, end))
            ).join(', ') +
            ' ' +
            key
          )
      })
    },
    datePeriods() {
      return this.periods
        .map((period) => {
          const start = new Date(period.start)
          const end = new Date(period.end)
          const sameYear = start.getFullYear() === end.getFullYear()

          return {
            ...period,
            start,
            end,
            key: start.getFullYear() + (sameYear ? '' : YEAR_JOINER + end.getFullYear()),
          }
        })
        .toSorted((a, b) => {
          const diff = a.start - b.start
          if (diff !== 0) return diff
          return a.end - b.end
        })
    },
    groupedPeriods() {
      return groupBy(this.datePeriods, 'key')
    },
  },
  methods: { campRoute },
}
</script>

<style scoped></style>
