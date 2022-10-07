<template>
  <div :class="landscape ? 'landscape tw-break-after-page' : 'tw-break-after-page'">
    <h1
      :id="`content_${index}_period_${period.id}`"
      class="tw-text-2xl tw-font-bold tw-mb-6"
    >
      {{ $tc('print.picasso.title') }} {{ $tc('entity.period.name') }}
      {{ period.description }}
    </h1>

    <v-sheet :width="landscape ? 960 : 680">
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
        :interval-height="landscape ? 23 : 35"
        interval-width="46"
        event-text-color="black"
        :locale="$i18n.locale"
        :interval-format="intervalFormat"
        :day-format="dayFormat"
        :weekday-format="weekdayFormat"
      >
        <!-- day header -->
        <template #day-label-header="{ date }">
          <span class="tw-block">
            {{ $date.utc(date).format($tc('global.datetime.dateLong')) }}
          </span>

          <span v-if="hasDayResponsibles(date)" class="tw-text-sm tw-italic">
            {{ $tc('entity.day.fields.dayResponsibles') }}:
            {{ dayResponsiblesCommaSeparated(date) }}
          </span>

          <!-- <day-responsibles :date="date" :period="period" :readonly="!editable" /> -->
        </template>

        <template #event="{ event }">
          <div class="tw-float-left tw-text-xs tw-font-weight-medium">
            <!-- link jumps to first instance of scheduleEntry within the document -->
            <a :href="`#scheduleEntry_${event.id}`">
              ({{ event.number }})&nbsp; {{ event.activity().category().short }}:&nbsp;
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
import { dayResponsiblesCommaSeparated } from '@/helpers/dayResponsibles.js'

export default {
  props: {
    period: { type: Object, required: true },
    start: { type: String, required: true },
    end: { type: String, required: true },
    events: { type: Array, required: true },
    index: { type: Number, required: true },
    landscape: { type: Boolean, required: true },
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
      return this.$date.utc(day.date).format(this.$tc('global.datetime.dateLong'))
    },
    weekdayFormat() {
      return ''
    },
    responsiblesCommaSeparated(scheduleEntry) {
      const responsibles = responsiblesCommaSeparated(
        scheduleEntry.activity(),
        this.$tc.bind(this)
      )

      if (responsibles === '') {
        return ''
      }

      return `[${responsibles}]`
    },

    dayResponsiblesCommaSeparated(date) {
      const day = this.getDayByDate(date)
      if (!day) return null
      return dayResponsiblesCommaSeparated(day, this.$tc.bind(this))
    },

    hasDayResponsibles(date) {
      const day = this.getDayByDate(date)
      if (!day) return false
      return day.dayResponsibles().items.length > 0
    },

    getDayByDate(date) {
      return this.period.days().items.find((day) => {
        return this.$date.utc(date).isSame(this.$date.utc(day.start), 'day')
      })
    },
  },
}
</script>

<style lang="scss">
.landscape {
  transform: rotate(-90deg);
  position: relative;
  top: 320px;
}

.v-calendar {
  overflow: visible;
}

.v-calendar-daily__body {
  overflow: visible;
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
  overflow-y: hidden;
  word-wrap: break-word;
  word-break: break-all;

  a {
    color: black;
  }
}
</style>
