<template>
  <v-row no-gutters class="picasso">
    <v-col cols="12">
      <div class="picasso">
        <h1>Picasso {{ period.description }}</h1>

        <v-sheet>
          <v-calendar
            ref="calendar"
            :value="today"
            :events="events"
            event-start="startTime"
            event-end="endTime"
            :event-color="getActivityColor"
            :start="period.start"
            :end="period.end"
            type="custom-daily"
            event-overlap-mode="column"
            first-interval="6"
            interval-count="18"
            interval-height="45"
            interval-width="100"
            event-text-color="black"
          >
            <template #event="{ event }">
              <div class="font-weight-medium mb-1">
                ({{ event.number }})&nbsp;
                {{ event.activity().category().short }}:&nbsp;
                {{ event.activity().title }}
              </div>
              <span>[smiley, forte]</span>
            </template>
          </v-calendar>
        </v-sheet>
      </div>
    </v-col>
  </v-row>
</template>

<script>
import { defineHelpers } from '~/../common/helpers/scheduleEntry/dateHelperLocal.js'

export default {
  props: {
    period: { type: Object, required: true },
  },
  data: () => ({
    today: '2019-01-08',
    events: [
      {
        number: '2.1',
        startTime: '2019-01-07 09:00',
        endTime: '2019-01-07 10:00',
        activity: () => ({
          title: 'Dummy activity',
          category: () => ({
            short: 'LA',
            color: '#4CAF50',
          }),
        }),
      },
      {
        number: '4.1',
        startTime: '2019-01-09 12:30',
        endTime: '2019-01-09 15:30',
        activity: () => ({
          title: 'Dummy activity',
          category: () => ({
            short: 'LS',
            color: '#99CCFF',
          }),
        }),
      },
      {
        number: '4.2',
        startTime: '2019-01-09 13:00',
        endTime: '2019-01-09 17:30',
        activity: () => ({
          title: 'Dummy activity',
          category: () => ({
            short: 'LS',
            color: '#99CCFF',
          }),
        }),
      },
    ],
  }),
  async fetch() {
    this.camp = await this.period.camp()._meta.load

    const [scheduleEntries, activities, categories] = await Promise.all([
      this.period.scheduleEntries().$loadItems(),
      this.camp.activities().$loadItems(),
      this.camp.categories().$loadItems(),
    ])

    this.events = scheduleEntries.items.map((entry) =>
      defineHelpers(entry, true)
    )
  },
  methods: {
    getActivityColor(scheduleEntry) {
      return scheduleEntry.activity().category().color
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
  font-size: 11px;
}
</style>
