<!--
Displays all periods of a single camp and allows to edit them & create new ones
-->

<template>
  <v-skeleton-loader v-if="camp().periods()._meta.loading" type="article" />
  <v-list v-else class="py-0">
    <template v-for="period in periods.items">
      <period-item
        :key="period._meta.self"
        class="px-0"
        :period="period"
        :disabled="disabled" />
      <v-divider :key="period._meta.self + 'div'" />
    </template>
  </v-list>
</template>

<script>
import PeriodItem from './CampPeriodsListItem.vue'

export default {
  name: 'CampPeriods',
  components: {
    PeriodItem
  },
  props: {
    camp: {
      type: Function,
      required: true
    },
    disabled: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {}
  },
  computed: {
    periods () {
      return this.camp().periods()
    }
  }
}
</script>

<style scoped>
</style>
