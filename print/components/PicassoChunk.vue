<template>
  <div class="tw-break-after-page">
    <h1
      :id="`content_${index}_period_${period.id}`"
      class="tw-text-2xl tw-font-bold tw-mb-6"
    >
      {{ $tc('print.picasso.title') }} {{ $tc('entity.period.name') }}
      {{ period.description }}
    </h1>

    <v-sheet>
      <v-calendar
        ref="calendar"
        :events="events"
        event-start="startTimestamp"
        event-end="endTimestamp"
        :event-color="getActivityColor"
        :start="start"
        :end="end"
        type="custom-daily"
        event-overlap-mode="column"
        first-interval="0"
        interval-count="24"
        interval-height="35"
        interval-width="46"
        event-text-color="black"
        :locale="$i18n.locale"
        :interval-format="intervalFormat"
        :day-format="dayFormat"
        :weekday-format="weekdayFormat"
      >
        <template #event="{ event }">
          <div class="tw-float-left tw-text-xs tw-font-weight-medium">
            <!-- link jumps to first instance of scheduleEntry within the document -->
            <a :href="`#scheduleEntry_${event.id}`">
              ({{ event.number }})&nbsp;
              {{ event.activity().category().short }}:&nbsp;
              {{ event.activity().title }}
            </a>
          </div>
          <span class="tw-float-right tw-text-xs tw-italic ml-1">{{
            responsiblesCommaSeparated(event)
          }}</span>
        </template>
      </v-calendar>
    </v-sheet>
  </div>
</template>

<script>
import { responsiblesCommaSeparated } from '@/helpers/activityResponsibles.js'

export default {
  props: {
    period: { type: Object, required: true },
    start: { type: String, required: true },
    end: { type: String, required: true },
    events: { type: Array, required: true },
    index: { type: Number, required: true },
  },

  methods: {
    getActivityColor(scheduleEntry) {
      return scheduleEntry.activity().category().color
    },
    intervalFormat(time) {
      return this.$date
        .utc(time.date + ' ' + time.time)
        .format(this.$tc('global.datetime.hourLong'))
    },
    dayFormat(day) {
      return this.$date
        .utc(day.date)
        .format(this.$tc('global.datetime.dateLong'))
    },
    weekdayFormat() {
      return ''
    },
    responsiblesCommaSeparated(scheduleEntry) {
      const responsibles = responsiblesCommaSeparated(scheduleEntry.activity())

      if (responsibles === '') {
        return ''
      }

      return `[${responsibles}]`
    },
  },
}
</script>

<style lang="scss">
@media print {
  @page picasso {
    /* changing page orientation currently not working in pagedJS/chrome
       https://github.com/pagedjs/pagedjs/issues/6 */
    size: a4 landscape;
    margin: 15mm 15mm;

    @top-center {
      content: 'Picasso';
    }
  }

  .picasso {
    page-break-after: always;
    page: picasso;
  }
}

.v-calendar-daily__pane {
  overflow-y: visible;
}

.v-calendar-daily__scroll-area {
  overflow-y: visible;
}

.v-calendar .v-event-timed {
  padding: 2px;
  white-space: normal;
  overflow-wrap: break-word;

  a {
    color: black;
  }
}
</style>
