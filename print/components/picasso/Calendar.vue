<template>
  <div
    role="grid"
    class="v-calendar v-calendar-daily theme--light v-calendar-events tw-h-full"
  >
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
        <div class="v-calendar-daily__pane tw-h-full">
          <div class="v-calendar-daily__day-container">
            <div class="v-calendar-daily__intervals-body" style="width: 46px">
              <div
                v-for="{ time, weight } in displayedTimes"
                :key="time"
                class="v-calendar-daily__interval"
                :style="`flex-grow: ${weight}`"
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
export default {
  props: {
    days: { type: Array, required: true },
    times: { type: Array, required: true },
    scheduleEntries: { type: Array, default: () => [] },
  },
  computed: {
    displayedTimes() {
      const displayedTimes = this.times.map(([time, weight], index) => {
        if (index === 0 || weight === 0) return { time: ' ', weight }
        return {
          weight,
          time: this.$date()
            .hour(0)
            .minute(time * 60)
            .second(0)
            .format(this.$t('global.datetime.hourLong')),
        }
      })

      return displayedTimes
    },
  },
}
</script>
