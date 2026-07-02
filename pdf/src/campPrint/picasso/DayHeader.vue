<template>
  <View>
    <Text class="picasso-day-header-text">{{ date }}</Text>
    <View v-if="showDayResponsibles" class="picasso-day-responsibles">
      <Text class="picasso-day-responsibles-text">{{ dayResponsibles }}</Text>
    </View>
  </View>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import {
  dayResponsiblesCommaSeparated,
  filterDayResponsiblesByDay,
} from '../../../common/helpers/dayResponsibles.js'

export default {
  name: 'DayHeader',
  extends: PdfComponent,
  props: {
    day: { type: Object, required: true },
    showDayResponsibles: { type: Boolean, default: false },
  },
  computed: {
    date() {
      return this.$date
        .utc(this.day.start)
        .hour(0)
        .minute(0)
        .second(0)
        .format(this.$tc('global.datetime.dateLong'))
    },
    dayResponsibles() {
      if (filterDayResponsiblesByDay(this.day).length === 0) return ''
      const label = this.$tc('entity.day.fields.dayResponsibles')
      const displayNames = dayResponsiblesCommaSeparated(this.day, this.$tc)
      return `${label}: ${displayNames}`
    },
  },
}
</script>
<style lang="react-pdf">
.picasso-day-header-text {
  font-size: 8pt;
  font-weight: bold;
  margin: 0 auto 2pt;
}
.picasso-day-responsibles {
  font-size: 8pt;
  margin: 3pt auto 0;
  line-height: 1.3;
}
.picasso-day-responsibles-text {
  padding-bottom: 5pt;
}
</style>
