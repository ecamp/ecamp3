<template>
  <v-list-item two-line :to="campRoute(camp)">
    <v-list-item-content>
      <v-list-item-title class="d-flex gap-x-2 justify-space-between">
        <strong class="whitespace-normal">{{ camp.title }}</strong>
        <span>{{ date }}</span>
      </v-list-item-title>
      <v-list-item-subtitle class="d-flex gap-2 flex-wrap-reverse justify-space-between">
        <span class="whitespace-normal">{{ camp.motto }}</span>
        <span>{{ camp.organizer }}</span>
      </v-list-item-subtitle>
    </v-list-item-content>
  </v-list-item>
</template>

<script>
import { campRoute } from '@/router.js'
export default {
  name: 'CampListItem',
  props: {
    camp: { type: Object, required: true },
  },
  computed: {
    periods() {
      return this.camp.periods().items
    },
    date() {
      if (!this.periods.length) return
      const formatMY = new Intl.DateTimeFormat(this.$i18n.locale, {
        year: 'numeric',
        month: 'short',
      })
      return [...this.periods]
        .sort((a, b) => new Date(a.start) - new Date(b.start))
        .map((period) => {
          return formatMY.formatRange(new Date(period.start), new Date(period.end))
        })
        .join(' | ')
    },
  },
  methods: { campRoute },
}
</script>

<style scoped></style>
