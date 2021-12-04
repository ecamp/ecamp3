<template>
  <v-row no-gutters class="picasso">
    <v-col cols="12">
      <div class="picasso">
        <h1>Picasso</h1>

        <v-sheet>
          <v-calendar
            ref="calendar"
            :value="today"
            :events="events"
            event-color="blue lighten-4"
            type="week"
            event-overlap-mode="column"
            first-interval="6"
            interval-count="18"
            interval-height="45"
            interval-width="100"
            event-text-color="black"
            event-color:
          >
            <template #event="{ event }">
              <div class="font-weight-medium mb-1">
                ({{ event.number }})&nbsp; {{ event.categoryShort }}:&nbsp;
                {{ event.name }}
              </div>
              <span>[{{ event.responsibles }}]</span>
            </template>
          </v-calendar>
        </v-sheet>
      </div>
    </v-col>
  </v-row>
</template>

<script>
export default {
  data: () => ({
    today: '2019-01-08',
    events: [
      {
        number: '2.1',
        name: 'Weekly Meeting',
        start: '2019-01-07 09:00',
        end: '2019-01-07 10:00',
        categoryShort: 'LA',
        responsibles: 'smiley, forte',
      },
      {
        name: "Thomas' Birthday",
        start: '2019-01-10',
      },
      {
        number: '4.1',
        name: 'Mash Potatoes',
        start: '2019-01-09 12:30',
        end: '2019-01-09 15:30',
        categoryShort: 'LA',
        responsibles: 'smiley, forte',
      },
      {
        number: '4.2',
        name: 'Spielenachmittag',
        start: '2019-01-09 13:00',
        end: '2019-01-09 17:30',
        categoryShort: 'LA',
        responsibles: 'smiley, forte',
      },
    ],
  }),
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
