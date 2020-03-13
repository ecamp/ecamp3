<!--
Listing all given event instances in a calendar view.
-->

<template>
  <v-calendar
    class="ec-picasso"
    :events="eventInstances"
    event-start="start_time"
    event-end="end_time"
    :event-name="getEventName | loading('Lädt…', ({ input }) => input.event()._meta.loading)"
    :event-color="getEventColor | loading('grey lighten-2', eventInstance => eventInstance.event()._meta.loading)"
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
    @click:event="showEventInstance" />
</template>
<script>
import { eventInstanceRoute } from '../router'

export default {
  name: 'Picasso',
  props: {
    camp: { type: Function, required: true },
    eventInstances: { type: Array, required: true },
    start: { type: Date, required: true },
    end: { type: Date, required: true },
    type: { type: String, required: false, default: 'week' },
    intervalHeight: { type: Number, required: false, default: 42 }
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
  mounted () {
    this.camp().events()
  },
  methods: {
    getEventName (event, _) {
      return '(' + event.input.number + ') ' + event.input.event().event_category().short + ': ' + event.input.event().title
    },
    getEventColor (event, _) {
      return event.event().event_category().color.toString()
    },
    getIntervalFormat (time) {
      return time.time
    },
    showEventInstance ({ event: eventInstance }) {
      this.$router.push(eventInstanceRoute(this.camp(), eventInstance))
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

  .v-calendar-daily {
    border-top: 0;
  }
</style>
