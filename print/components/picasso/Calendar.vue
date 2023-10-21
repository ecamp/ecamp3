<template>
  <div role="grid" class="v-calendar v-calendar-daily theme--light v-calendar-events">
    <div class="v-calendar-daily__head" style="margin-right: 0px">
      <div class="v-calendar-daily__intervals-head" style="width: 46px" />
      <div v-for="day in days" :key="day.id" class="v-calendar-daily_head-day v-future">
        <div class="v-calendar-daily_head-weekday" />
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
                <div class="v-calendar-daily__interval-text">
                  {{ time }}
                </div>
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
import { timesWeightsSum } from '../../../common/helpers/picasso.js'

export default {
  props: {
    days: { type: Array, required: true },
    times: { type: Array, required: true },
    scheduleEntries: { type: Array, default: () => [] },
    contentHeight: { type: Number, default: 0 },
  },
  computed: {
    intervalHeight() {
      return this.contentHeight / timesWeightsSum(this.times)
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
