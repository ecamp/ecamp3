<template>
  <div role="grid" class="v-calendar v-calendar-daily theme--light v-calendar-events">
    <div class="v-calendar-daily__head" style="margin-right: 0px">
      <div class="v-calendar-daily__intervals-head" style="width: 46px"></div>
      <div v-for="day in days" :key="day.id" class="v-calendar-daily_head-day v-future">
        <div class="v-calendar-daily_head-weekday"></div>
        <div class="v-calendar-daily_head-day-label">
          <PicassoCalendarDayHeader :day="day" />
        </div>
      </div>
    </div>
    <div class="v-calendar-daily__body">
      <div class="v-calendar-daily__scroll-area">
        <div class="v-calendar-daily__pane" :style="`height: ${contentHeight}px`">
          <div class="v-calendar-daily__day-container">
            <div class="v-calendar-daily__intervals-body" style="width: 46px">
              <div
                v-for="{ time, height } in displayedTimes"
                :key="time"
                class="v-calendar-daily__interval"
                :style="`height: ${height}px`"
              >
                <div class="v-calendar-daily__interval-text">{{ time }}</div>
              </div>
            </div>
            <picasso-calendar-day
              v-for="day in days"
              :key="day.id"
              :times="times"
              :displayed-times="displayedTimes"
              :day="day"
              :schedule-entries="scheduleEntries"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    days: { type: Array, required: true },
    bedtime: { type: Number, default: 0 },
    getUpTime: { type: Number, default: 24 },
    timeStep: { type: Number, default: 1 },
    scheduleEntries: { type: Array, default: () => [] },
    contentHeight: { type: Number, default: 0 },
  },
  computed: {
    intervalHeight() {
      const totalWeight = this.times.reduce((total, time) => {
        return total + time[1]
      }, 0)
      return this.contentHeight / totalWeight
    },

    /**
     * Generates an array of time row descriptions, used for labeling the vertical axis of the picasso.
     * Format of each array element: [hour, weight] where weight determines how tall the time row is rendered.
     *
     * @returns {*[[hour: number, weight: number]]}
     */
    times() {
      const times = [[this.getUpTime - this.timeStep / 2, 0.5]]
      for (let i = 0; this.getUpTime + i * this.timeStep < this.bedtime; i++) {
        // TODO The weight could also be generated depending on the schedule entries present in the camp:
        //   e.g. give less weight to hours that contain no schedule entries.
        const weight = 1
        times.push([this.getUpTime + i * this.timeStep, weight])
      }
      times.push([this.bedtime, 0.5])
      // this last hour is only needed for defining the length of the day. The weight should be 0.
      times.push([this.bedtime + this.timeStep / 2, 0])

      return times
    },

    displayedTimes() {
      const displayedTimes = this.times.map(([time, weight], index) => {
        if (index === 0 || weight === 0)
          return { time: ' ', weight, height: weight * this.intervalHeight }
        return {
          weight,
          height: weight * this.intervalHeight,
          time: this.$date()
            .hour(0)
            .minute(time * 60)
            .second(0)
            .format(this.$tc('global.datetime.hourLong')),
        }
      })

      return displayedTimes
    },
  },
}
</script>
