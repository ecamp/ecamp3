<template>
  <v-row no-gutters class="picasso">
    <v-col cols="12">
      <div class="picasso">
        <h1>Picasso</h1>

        <v-sheet>
          <v-calendar
            ref="calendar"
            :now="today"
            :value="today"
            :events="events"
            color="primary"
            type="4day"
            event-overlap-mode="column"
            first-interval="6"
            interval-count="18"
            interval-height="25"
          />
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
        name: 'Weekly Meeting',
        start: '2019-01-07 09:00',
        end: '2019-01-07 10:00',
      },
      {
        name: "Thomas' Birthday",
        start: '2019-01-10',
      },
      {
        name: 'Mash Potatoes',
        start: '2019-01-09 12:30',
        end: '2019-01-09 15:30',
      },
      {
        name: 'Spielenachmittag',
        start: '2019-01-09 13:00',
        end: '2019-01-09 17:30',
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
</style>
