<!--
Listing all given activity schedule entries in a calendar view.
-->

<template>
  <div>
    <v-calendar
      ref="calendar"
      v-model="value"
      v-resize="resize"
      :class="['e-picasso', editable && 'e-picasso--editable']"
      :events="events"
      event-overlap-mode="column"
      event-start="startTimestamp"
      event-end="endTimestamp"
      :interval-height="computedIntervalHeight"
      interval-width="46"
      :interval-format="intervalFormat"
      :first-interval="firstInterval"
      :interval-count="intervalCount"
      :interval-minutes="intervalMinutes"
      :start="start"
      :end="end"
      :locale="$i18n.locale"
      :day-format="dayFormat"
      :type="type"
      :max-days="maxDays"
      :weekday-format="weekdayFormat"
      :weekdays="[1, 2, 3, 4, 5, 6, 0]"
      color="primary"
      :event-ripple="false"
      v-on="vCalendarListeners"
      @mouseleave.native="onMouseleave"
      @mousedown.native.prevent="
        /*this prevents from middle button to start scroll behavior*/
      "
    >
      <!-- day header -->
      <template #day-label-header="{ date }">
        <div class="e-picasso-daily_head-day-label">
          <span v-if="widthPluralization > 0" class="d-block">
            {{ $date.utc(date).format('dddd') }}
          </span>
          {{
            $date
              .utc(date)
              .format(
                $tc(
                  'components.program.picasso.picasso.datetime.date',
                  widthPluralization
                )
              )
          }}
        </div>
        <day-responsibles :date="date" :period="period" :readonly="!editable" />
      </template>

      <!-- template for single scheduleEntry -->
      <template #event="{ event }">
        <PicassoEntry
          :show-avatars="showAvatars"
          :schedule-entry="event"
          :editable="editable"
          @startResize="startResize(event)"
          @finishEdit="reloadScheduleEntries"
        />
      </template>
    </v-calendar>

    <v-snackbar v-model="isSaving" light>
      <v-icon class="mdi-spin">mdi-loading</v-icon>
      {{ $tc('global.button.saving') }}
    </v-snackbar>
  </div>
</template>
<script>
import Vue, { reactive, ref, toRefs, watch } from 'vue'
import { useDragAndDropMove } from './useDragAndDropMove.js'
import { useDragAndDropResize } from './useDragAndDropResize.js'
import { useDragAndDropNew } from './useDragAndDropNew.js'
import { useDragAndDropReminder } from './useDragAndDropReminder.js'
import { apiStore as api } from '@/plugins/store'
import mergeListeners from '@/helpers/mergeListeners.js'
import {
  timestampToUtcString,
  utcStringToTimestamp,
} from '@/common/helpers/dateHelperVCalendar.js'
import DayResponsibles from './DayResponsibles.vue'
import { ONE_DAY } from '@/helpers/vCalendarDragAndDrop.js'
import { errorToMultiLineToast } from '@/components/toast/toasts'
import PicassoEntry from './PicassoEntry.vue'

export default {
  name: 'Picasso',
  components: {
    PicassoEntry,
    DayResponsibles,
  },
  props: {
    // period for which to show picasso
    period: {
      type: Object,
      required: true,
    },

    // collection of scheduleEntries
    scheduleEntries: {
      type: Object,
      required: true,
    },

    // false disables drag & drop and disabled edit dialogs
    editable: {
      type: Boolean,
      required: false,
      default: false,
    },

    // v-calendar start: starting date (first day)
    start: {
      type: String,
      required: true,
    },

    // v-calender end: end date (last day)
    end: {
      type: String,
      required: true,
    },

    // v-calendar type
    type: {
      type: String,
      required: false,
      default: 'custom-daily',
    },

    // v-calendar intervalHeight
    intervalHeight: {
      type: Number,
      required: false,
      default: null,
    },
  },

  // emitted events
  emits: [
    'newEntry', // triggered once when a new entry was created via drag & drop (parameters: startTimestamp, endTimestamp)
    'unlockReminder', // triggered when we think someone is trying to create/move in non-editable mode
  ],

  // composition API setup
  setup(props, { emit }) {
    const { editable, scheduleEntries } = toRefs(props)

    const isSaving = ref(false)

    const showAvatars = new URLSearchParams(document.location.search).has('showAvatars')

    // callback used to save entry to API
    const updateEntry = (scheduleEntry, startTimestamp, endTimestamp) => {
      const patchData = {
        start: timestampToUtcString(startTimestamp),
        end: timestampToUtcString(endTimestamp),
      }
      isSaving.value = true
      api
        .patch(scheduleEntry._meta.self, patchData)
        .catch((error) => {
          Vue.$toast.error(errorToMultiLineToast(error))
        })
        .finally(() => {
          isSaving.value = false
          reloadScheduleEntries()
        })
    }

    // callback used to create new entry
    const placeholder = reactive({
      startTimestamp: 0,
      endTimestamp: 0,
      timed: true,
      tmpEvent: true,
    })
    const createEntry = (startTimestamp, endTimestamp, finished) => {
      const start = timestampToUtcString(startTimestamp)
      const end = timestampToUtcString(endTimestamp)

      if (finished) {
        placeholder.startTimestamp = 0
        placeholder.endTimestamp = 0
        emit('newEntry', start, end)
      } else {
        placeholder.startTimestamp = startTimestamp
        placeholder.endTimestamp = endTimestamp
      }
    }

    const showReminder = (move) => {
      emit('unlockReminder', move)
    }

    const calenderStartTimestamp = utcStringToTimestamp(props.start)
    const calendarEndTimestamp = utcStringToTimestamp(props.end) + ONE_DAY

    const dragAndDropMove = useDragAndDropMove(
      editable,
      5,
      updateEntry,
      calenderStartTimestamp,
      calendarEndTimestamp
    )
    const dragAndDropResize = useDragAndDropResize(
      editable,
      updateEntry,
      calenderStartTimestamp,
      calendarEndTimestamp
    )
    const dragAndDropNew = useDragAndDropNew(editable, createEntry)
    const dragAndDropReminder = useDragAndDropReminder(editable, showReminder)

    // merge mouseleave handlers
    // this is needed, because .native modifiers doesn't work with v-on property
    // https://github.com/vuejs/vue/issues/5578#issuecomment-516932359
    const onMouseleave = () => {
      dragAndDropMove.nativeMouseLeave()
      dragAndDropResize.nativeMouseLeave()
      dragAndDropNew.nativeMouseLeave()
    }

    // merge v-calendar listeners
    const vCalendarListeners = mergeListeners([
      dragAndDropMove.vCalendarListeners,
      dragAndDropResize.vCalendarListeners,
      dragAndDropNew.vCalendarListeners,
      dragAndDropReminder.vCalendarListeners,
    ])

    // make events a reactive array + load event array from schedule entries
    const events = ref([])
    const loadCalenderEventsFromScheduleEntries = () => {
      // prepare scheduleEntries to make them understandable by v-calendar
      events.value = scheduleEntries.value.items.map((entry) => ({
        ...entry,
        startTimestamp: utcStringToTimestamp(entry.start),
        endTimestamp: utcStringToTimestamp(entry.end),
        timed: true,
      }))

      // add placeholder for drag & drop (create new entry)
      events.value.push(placeholder)
    }
    loadCalenderEventsFromScheduleEntries()

    // watch for changes from API
    watch(scheduleEntries, loadCalenderEventsFromScheduleEntries)

    // reloads schedule entries from API + recreates event array after reload
    const reloadScheduleEntries = async () => {
      await api.reload(scheduleEntries.value)
      loadCalenderEventsFromScheduleEntries()
    }

    return {
      showAvatars,
      vCalendarListeners,
      startResize: dragAndDropResize.startResize,
      onMouseleave,
      isSaving,
      reloadScheduleEntries,
      loadCalenderEventsFromScheduleEntries,
      events,
    }
  },
  data() {
    return {
      maxDays: 100,
      entryWidth: 80,
      value: '',
      activitiesLoading: true,
      categoriesLoading: true,

      // interval configuration for v-calendar
      // only 0-24 supported at the moment, until https://github.com/vuetifyjs/vuetify/issues/14603 is resolved
      intervalMinutes: 60,
      firstInterval: 0,
      intervalCount: 24,
    }
  },
  computed: {
    widthPluralization() {
      if (this.entryWidth < 81) {
        return 0
      } else if (this.entryWidth < 85) {
        return 1
      } else {
        return 2
      }
    },
    camp() {
      return this.period.camp()
    },
    computedIntervalHeight() {
      return this.intervalHeight ?? this.$vuetify.breakpoint.xsOnly
        ? (1.3 * (this.$vuetify.breakpoint.height - 140)) / this.intervalCount
        : 1.3 * Math.max((this.$vuetify.breakpoint.height - 204) / this.intervalCount, 32)
    },
  },
  mounted() {
    this.period
      .camp()
      .activities()
      ._meta.load.then(() => {
        this.activitiesLoading = false
      })
    this.period
      .camp()
      .categories()
      ._meta.load.then(() => {
        this.categoriesLoading = false
      })

    // scroll a bit down to hide the night hours
    const scroller = this.$el.querySelector('.v-calendar')
    scroller.scrollTo({ top: 250 })
  },
  methods: {
    resize() {
      const widthIntervals = 46
      this.entryWidth = Math.max(
        (this.$refs.calendar.$el.offsetWidth - widthIntervals) /
          this.$refs.calendar.days.length,
        80
      )
    },
    intervalFormat(time) {
      return this.$date
        .utc(time.date + ' ' + time.time)
        .format(this.$tc('global.datetime.hourLong'))
    },
    dayFormat(day) {
      if (this.$vuetify.breakpoint.smAndDown) {
        return this.$date.utc(day.date).format(this.$tc('global.datetime.dateShort'))
      } else {
        return this.$date.utc(day.date).format(this.$tc('global.datetime.dateLong'))
      }
    },
    weekdayFormat() {
      return ''
    },
  },
}
</script>

<style scoped lang="scss">
.e-picasso {
  border: none;
  overflow: auto;

  @media #{map-get($display-breakpoints, 'xs-only')} {
    position: fixed;
    height: inherit;
    top: 48px;
    bottom: calc(56px + env(safe-area-inset-bottom));
    left: 0;
    right: 0;
  }

  @media #{map-get($display-breakpoints, 'sm-and-up')} {
    height: calc(100vh - 136px);
  }

  @media #{map-get($display-breakpoints, 'md-and-up')} {
    height: calc(100vh - 168px);
  }

  :deep {
    .v-calendar-daily_head-day,
    .v-calendar-daily__day {
      min-width: 80px;
    }

    .v-event-timed {
      padding: 0px;
      font-size: 11px !important;
      white-space: normal;
      line-height: 1.15;
      user-select: none;
      -webkit-user-select: none;
      border-color: white !important;
    }

    .v-calendar-daily__day-container {
      width: initial;
    }

    .v-calendar-daily__head,
    .v-calendar-daily__intervals-body {
      position: sticky;
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(4px);
    }

    .v-calendar-daily__head {
      top: 0;
      z-index: 2;
      min-width: fit-content;
      overflow: hidden;
    }

    .v-calendar-daily__pane,
    .v-calendar-daily__body {
      overflow: visible;
    }

    .v-calendar-daily__intervals-body {
      left: 0;
      z-index: 1;
    }

    .v-calendar-daily__scroll-area {
      overflow-y: visible;
    }
  }
}

// day title
.e-picasso-daily_head-day-label {
  font-size: 11px;
  font-feature-settings: 'tnum';
  letter-spacing: -0.1px;
}

:deep(.v-calendar-daily_head-day-label) {
  height: 100%;
  display: flex;
  flex-direction: column;

  .v-text-field .v-label {
    text-align: center;
    width: 100%;
    transform-origin: top center;
  }

  .e-form-container {
    display: contents;
  }

  .v-select {
    flex-direction: column;
  }

  .v-input__slot {
    align-items: stretch;
    height: 100%;
  }

  .v-select__slot {
    flex-wrap: wrap;
    align-content: space-between;
  }

  .v-select__selections {
    gap: 4px;
    padding: 4px 2px;
    width: 100%;
    min-width: initial;

    .v-chip {
      margin: 0;
      padding-left: 6px;
      padding-right: 6px;
    }

    > input {
      height: 1px;
      padding: 0;
      margin: 0;
    }
  }
}
</style>
