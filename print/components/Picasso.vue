<template>
  <v-row no-gutters class="picasso">
    <v-col cols="12">
      <div :class="rotate ? 'rotate' : ''">
        <v-sheet>
          <h1
            :id="`content_${index}_period_${period.id}`"
            class="tw-text-2xl tw-font-bold"
          >
            {{ $tc('print.picasso.title') }}: {{ $tc('entity.period.name') }}
            {{ period.description }}
          </h1>

          <v-calendar
            ref="calendar"
            :now="today"
            :value="today"
            :events="events"
            color="primary"
            event-overlap-mode="column"
            first-interval="6"
            interval-count="18"
            interval-height="29"
            v-bind="$attrs"
          />
        </v-sheet>
      </div>
    </v-col>
  </v-row>
</template>

<script>
export default {
  inheritAttrs: false,
  props: {
    rotate: {
      type: Boolean,
      required: false,
      default: false,
    },
    period: {
      type: Object,
      required: true,
    },
    index: { type: Number, required: true },
  },
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

<style lang="scss" scoped>
.rotate {
  -webkit-transform: rotate(-90deg);
  -moz-transform: rotate(-90deg);
  -o-transform: rotate(-90deg);
  -ms-transform: rotate(-90deg);
  transform: rotate(-90deg);

  .v-sheet {
    width: 1000px;
    position: relative;
    left: -320px;
  }
}
</style>
<style lang="scss">
@media print {
  @page picasso {
    /* changing page orientation currently not working in pagedJS/chrome
       https://github.com/pagedjs/pagedjs/issues/6 */
    size: a4 landscape;
    margin: 15mm 10mm 10mm 10mm;

    @top-center {
      content: 'Picasso';
      z-index: 100;
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
