<!--
Listing all given activity schedule entries in a calendar view.
-->

<template>
  <v-calendar
    class="ec-picasso"
    :events="scheduleEntries"
    event-start="startTime"
    event-end="endTime"
    :event-name="getActivityName | loading('Lädt…', ({ input }) => input.activity()._meta.loading)"
    :event-color="getActivityColor | loading('grey lighten-2', scheduleEntry => scheduleEntry.activity()._meta.loading)"
    :interval-height="intervalHeight"
    interval-width="46"
    :interval-format="getIntervalFormat"
    first-interval="5"
    interval-count="19"
    :start="startDateString"
    :end="endDateString"
    locale="de-ch"
    :day-format="dayFormat"
    :type="type"
    :weekday-format="weekdayFormat"
    :weekdays="[1, 2, 3, 4, 5, 6, 0]"
    color="primary"
    @click:event="showScheduleEntry" />
</template>
<script>
import { scheduleEntryRoute } from '@/router'

export default {
  name: 'Picasso',
  props: {
    camp: {
      type: Function,
      required: true
    },
    scheduleEntries: {
      type: Array,
      required: true
    },
    start: {
      type: Date,
      required: true
    },
    end: {
      type: Date,
      required: true
    },
    type: {
      type: String,
      required: false,
      default: 'week'
    },
    intervalHeight: {
      type: Number,
      required: false,
      default: 42
    }
  },
  data () {
    return {
      weekdayShort: ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So']
    }
  },
  computed: {
    startDateString () {
      return this.formatDateForProp(this.start)
    },
    endDateString () {
      return this.formatDateForProp(this.end)
    }
  },
  methods: {
    getActivityName (event, _) {
      return '(' + event.input.number + ') ' + event.input.activity().activityCategory().short + ': ' + event.input.activity().title
    },
    getActivityColor (event, _) {
      return event.activity().activityCategory().color.toString()
    },
    getIntervalFormat (time) {
      return time.time
    },
    showScheduleEntry ({ event: scheduleEntry }) {
      this.$router.push(scheduleEntryRoute(this.camp(), scheduleEntry))
    },
    dayFormat (day) {
      if (this.$vuetify.breakpoint.smAndDown) {
        return this.weekdayShort[day.weekday] + ',\n' + day.day + '.' + day.month
      } else {
        return this.weekdayShort[day.weekday] + ', ' + day.day + '.' + day.month + '.' + day.year
      }
    },
    weekdayFormat (day) {
      return ''
    },
    formatDateForProp (date) {
      return '' + date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate()
    }
  }
}
</script>
<style lang="scss">
  .ec-picasso {
    .v-calendar-daily_head-day-label button.v-btn {
      height: auto;
      opacity: .75;
      border-radius: 0;
      padding: 2px;
      white-space: normal;
      min-width: auto;
      width: 100%;

      .v-btn__content {
        width: 100%;
      }
    }

    .v-event-timed {
      font-size: 11px !important;
      white-space: normal;
      line-height: 1.15;

      .pl-1 {
        padding-left: 2px !important;
      }
    }
  }

  @media #{map-get($display-breakpoints, 'xs-only')}{
    .ec-picasso .v-calendar-daily_head-day-label button.v-btn {
      font-size: 12px;
    }
  }
</style>
<style lang="scss" scoped>
  .v-card {
    overflow: hidden;
  }

  ::v-deep .v-calendar-daily__scroll-area {
    overflow-y: auto;
  }

  .v-calendar-daily {
    border-top: 0;
  }
</style>
