<template>
  <View class="picasso-day-column">
    <View class="picasso-day-column-grid">
      <View
        v-for="([_, weight], index) in times"
        class="picasso-day-column-grid-row"
        :class="{ 'picasso-day-column-grid-row-grey': index % 2 === 1 }"
        :style="{ flexGrow: weight }"
      ></View>
    </View>
    <View class="picasso-day-column-schedule-entry-container">
      <PicassoScheduleEntry
        v-for="{ scheduleEntry, variant, style, percentageHeight } in scheduleEntryItems"
        :key="scheduleEntry.id"
        :schedule-entry="scheduleEntry"
        :style="style"
        :variant="variant"
        :percentage-height="percentageHeight"
        :avatars="avatars"
        :config="config"
        :show-y-s-physical-activity-duration="showYSPhysicalActivityDuration"
      />
    </View>
  </View>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import PicassoScheduleEntry from './PicassoScheduleEntry.vue'
import {
  filterScheduleEntriesByDay,
  dayStart,
  dayEnd,
  positionStyles,
} from '../../../common/helpers/picasso.js'

export default {
  name: 'DayColumn',
  components: { PicassoScheduleEntry },
  extends: PdfComponent,
  props: {
    times: { type: Array, required: true },
    day: { type: Object, required: true },
    scheduleEntries: { type: Array, default: () => [] },
    avatars: { type: Boolean, default: false },
    config: { type: Object, required: true },
    showYSPhysicalActivityDuration: { type: Boolean, default: false },
  },
  computed: {
    relevantScheduleEntries() {
      return filterScheduleEntriesByDay(this.scheduleEntries, this.day, this.times)
    },
    scheduleEntryItems() {
      const entries = this.relevantScheduleEntries
      const positions = positionStyles(entries, this.day, this.times)
      const from = dayStart(this.day, this.times)
      const to = dayEnd(this.day, this.times)
      return entries.map((scheduleEntry) => {
        const start = this.$date.utc(scheduleEntry.start)
        const startsOnThisDay = start.isSameOrAfter(from) && start.isSameOrBefore(to)

        const end = this.$date.utc(scheduleEntry.end)
        const endsOnThisDay = end.isSameOrAfter(from) && end.isSameOrBefore(to)

        const { top, right, bottom, left, percentageHeight } = positions[scheduleEntry.id]

        let variant = 'fullday'
        if (startsOnThisDay && endsOnThisDay) variant = 'default'
        else if (startsOnThisDay) variant = 'start'
        else if (endsOnThisDay) variant = 'end'
        return {
          scheduleEntry,
          variant,
          style: { top, right, bottom, left },
          percentageHeight,
        }
      })
    },
  },
}
</script>
<style lang="react-pdf">
.picasso-day-column {
  flex-basis: 0;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  position: relative;
}
.picasso-day-column-grid {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  position: relative;
}
.picasso-day-column-grid-row {
  display: flex;
  flex-basis: 0;
}
.picasso-day-column-grid-row-grey {
  background-color: #eceff1;
}
.picasso-day-column-schedule-entry-container {
  margin: 0 4pt 0 0;
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
}
</style>
