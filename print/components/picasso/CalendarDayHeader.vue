<template>
  <div>
    <span class="tw-block">
      {{ $date.utc(date).format($t('global.datetime.dateLong')) }}
    </span>

    <span v-if="hasDayResponsibles" class="text-xs-relative">
      {{ $t('entity.day.fields.dayResponsibles') }}:
      {{ dayResponsiblesCommaSeparated }}
    </span>
  </div>
</template>

<script>
import {
  dayResponsiblesCommaSeparated,
  filterDayResponsiblesByDay,
} from '@/common/helpers/dayResponsibles.js'

export default {
  props: {
    day: { type: Object, required: true },
  },
  computed: {
    date() {
      return this.day.start
    },
    hasDayResponsibles() {
      return filterDayResponsiblesByDay(this.day).length > 0
    },
    dayResponsiblesCommaSeparated() {
      return dayResponsiblesCommaSeparated(this.day, this.$t.bind(this))
    },
  },
}
</script>
