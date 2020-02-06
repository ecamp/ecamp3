<!--
Listing all event instances of a single camp.
-->

<template>
  <v-card>
    <v-toolbar dense class="mb-3" color="blue-grey lighten-5">
      <v-icon left>
        mdi-account-group
      </v-icon>
      <v-toolbar-title class="overflow-visible">
        Picasso
      </v-toolbar-title>
      <v-tabs v-model="tab" right
              center-active background-color="blue-grey lighten-5">
        <v-tab v-for="period in periods.items"
               :key="period.id">
          {{ period.description }}
        </v-tab>
      </v-tabs>
    </v-toolbar>
    <v-tabs-items v-if="!events._meta.loading" v-model="tab">
      <v-tab-item v-for="period in periods.items"
                  :key="period.id">
        <v-calendar
          class="ec-picasso"
          :events="period.event_instances().items"
          event-start="start_time"
          event-end="end_time"
          :event-name="getEventName"
          :event-color="getEventColor"
          interval-height="42"
          :interval-format="getIntervalFormat"
          first-interval="5"
          interval-count="19"
          :start="period.start + ' 00:00:00'"
          :end="period.end + ' 00:00:00'"
          locale="de-ch"
          :day-format="dayFormat"
          type="week"
          :weekday-format="weekdayFormat"
          :weekdays="[1, 2, 3, 4, 5, 6, 0]"
          color="primary"
          @click:event="showEvent" />
      </v-tab-item>
    </v-tabs-items>
    <v-skeleton-loader v-if="events._meta.loading" class="ma-3"
                       type="table-thead,table-row@6" />
  </v-card>
</template>
<script>
import { eventRoute } from '@/router'

export default {
  name: 'Picassso',
  props: {
    camp: { type: Function, required: true }
  },
  data () {
    return {
      tab: null,
      editing: false,
      messages: [],
      weekdayShort: ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So']
    }
  },
  computed: {
    periods () {
      return this.camp().periods()
    },
    events () {
      return this.camp().events()
    }
  },
  methods: {
    getEventName (event, timed) {
      return '(' + event.input.number + ') ' + event.input.event().event_category().short + ': ' + event.input.event().title
    },
    getEventColor (event, timed) {
      return event.event().event_category().color.toString()
    },
    getIntervalFormat (time) {
      return time.time
    },
    showEvent ({ event: eventInstance }) {
      this.$router.push(eventRoute(this.camp(), eventInstance))
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
      white-space: pre;
      min-width: auto;

      .v-btn__content {
        max-width: 100%;
        white-space: normal;
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
