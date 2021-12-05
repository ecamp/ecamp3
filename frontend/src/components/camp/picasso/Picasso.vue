<!--
Listing all given activity schedule entries in a calendar view.
-->

<template>
  <div>
    <v-calendar
      ref="calendar"
      v-model="value"
      v-resize="resize"
      class="ec-picasso"
      :events="scheduleEntries"
      :event-name="getActivityName"
      :event-color="getActivityColor"
      event-start="startTime"
      event-end="endTime"
      :interval-height="computedIntervalHeight"
      interval-width="46"
      :interval-format="intervalFormat"
      first-interval="5"
      interval-count="19"
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
      @mousedown:event="entryMouseDown"
      @click:event="entryClick"
      @mousedown:time="timeMouseDown"
      @mousemove:time="timeMouseMove"
      @mouseup:time="timeMouseUp"
      @mouseleave.native="nativeMouseUp">
      <template #day-label-header="time">
        <div class="ec-daily_head-day-label">
          <span v-if="widthPluralization > 0" class="d-block">
            {{ $date.utc(time.date).format('dddd') }}
          </span> {{ $date.utc(time.date).format($tc('components.camp.picasso.datetime.date', widthPluralization)) }}
        </div>
      </template>
      <template #day-body="{ date }">
        <div v-if="dateNow === date"
             class="v-current-time" :style="{ top: nowY }" />
      </template>

      <!-- template for single scheduleEntry -->
      <template #event="{event, timed}">
        <!-- edit button & dialog -->
        <dialog-activity-edit
          v-if="editable && !event.tmpEvent"
          :schedule-entry="event">
          <template #activator="{ on }">
            <v-btn absolute
                   top
                   right x-small
                   dark text
                   class="ec-event--btn rounded-sm"
                   @click.prevent="on.click"
                   @mousedown.stop=""
                   @mouseup.stop="">
              <v-icon x-small>mdi-pencil</v-icon>
            </v-btn>
          </template>
        </dialog-activity-edit>

        <h4 class="v-event-title">
          {{ getActivityName(event) }}
        </h4>

        <!-- extendBottom: used for resizing -->
        <div
          v-if="editable && timed"
          class="v-event-drag-bottom"
          @mousedown.stop="extendBottom(event)" />
      </template>
    </v-calendar>

    <v-snackbar v-model="isSaving" light>
      <template v-if="patchError">
        <v-icon>mdi-alert</v-icon>
        {{ patchError }}
      </template>
      <template v-else>
        <v-icon class="mdi-spin">mdi-loading</v-icon>
        {{ $tc('global.button.saving') }}
      </template>
    </v-snackbar>
  </div>
</template>
<script>
import { toRefs } from '@vue/composition-api'
import useDragAndDrop from './useDragAndDrop.js'
import { isCssColor } from 'vuetify/lib/util/colorUtils'

import DialogActivityEdit from '@/components/dialog/DialogActivityEdit.vue'

export default {
  name: 'Picasso',
  components: {
    DialogActivityEdit
  },
  props: {
    // period for which to schow picasso
    period: {
      type: Object,
      required: true
    },

    // list of scheduleEntries
    scheduleEntries: {
      type: Array,
      required: true
    },

    // false disables drag & drop and disabled edit dialogs
    editable: {
      type: Boolean,
      required: false,
      default: false
    },

    // v-calendar start: starting date (first day)
    start: {
      type: Number,
      required: true
    },

    // v-calender end: end date (last day)
    end: {
      type: Number,
      required: true
    },

    // v-calendar type
    type: {
      type: String,
      required: false,
      default: 'custom-daily'
    },

    // v-calendar intervalHeight
    intervalHeight: {
      type: Number,
      required: false,
      default: 0
    }

  },

  // emitted events
  emits: [
    'changePlaceholder', // triggered continuously while a new entry is being dragged (parameters: startTime, endTime)

    'newEntry', // triggered once when a new entry was created via drag & drop (parameters: startTime, endTime)

    'openEntry' // triggered when an existing entry is clicked on (parameters: entry, newTab)
  ],

  // composition API setup
  setup (props, { emit }) {
    const { editable } = toRefs(props)

    const {
      entryMouseDown,
      timeMouseDown,
      timeMouseMove,
      timeMouseUp,
      nativeMouseUp,
      extendBottom,
      isSaving,
      patchError
    } = useDragAndDrop(editable, emit)

    return {
      // methods
      entryMouseDown,
      timeMouseDown,
      timeMouseMove,
      timeMouseUp,
      nativeMouseUp,
      extendBottom,

      // data
      isSaving,
      patchError
    }
  },
  data () {
    return {
      maxDays: 100,
      entryWidth: 80,
      value: '',
      activitiesLoading: true
    }
  },
  computed: {
    widthPluralization () {
      if (this.entryWidth < 81) {
        return 0
      } else if (this.entryWidth < 85) {
        return 1
      } else {
        return 2
      }
    },
    now () {
      return this.$refs.calendar ? this.$refs.calendar.times.now : null
    },
    dateNow () {
      return this.$refs.calendar ? this.$refs.calendar.times.now.date : null
    },
    nowY () {
      return this.$refs.calendar ? this.$refs.calendar.timeToY(this.now) + 'px' : '-10px'
    },
    camp () {
      return this.period.camp()
    },
    computedIntervalHeight () {
      return this.intervalHeight !== 0 ? this.intervalHeight : this.$vuetify.breakpoint.xsOnly ? (this.$vuetify.breakpoint.height - 140) / 19 : Math.max((this.$vuetify.breakpoint.height - 204) / 19, 32)
    }
  },
  mounted () {
    this.api.get().activities({ period: this.period._meta.self })._meta.load.then(() => { this.activitiesLoading = false })
  },
  methods: {
    resize () {
      const widthIntervals = 46
      this.entryWidth = Math.max((this.$refs.calendar.$el.offsetWidth - widthIntervals) / this.$refs.calendar.days.length, 80)
    },
    getActivityName (scheduleEntry, _) {
      if (this.isActivityLoading(scheduleEntry)) return this.$tc('global.loading')
      return (scheduleEntry.number ? scheduleEntry.number + ' ' : '') +
        (scheduleEntry.activity().category().short ? scheduleEntry.activity().category().short + ': ' : '') +
        scheduleEntry.activity().title
    },
    getActivityColor (scheduleEntry, _) {
      if (this.isCategoryLoading(scheduleEntry)) return 'grey lighten-1'
      const color = scheduleEntry.activity().category().color
      return isCssColor(color) ? color : color + ' elevation-4 v-event--temporary'
    },
    isActivityLoading (scheduleEntry) {
      return this.activitiesLoading || (scheduleEntry.activity()?._meta?.loading ?? false)
    },
    isCategoryLoading (scheduleEntry) {
      return scheduleEntry.activity().category()?._meta?.loading ?? false
    },
    intervalFormat (time) {
      return this.$date.utc(time.date + ' ' + time.time).format(this.$tc('global.datetime.hourLong'))
    },
    dayFormat (day) {
      if (this.$vuetify.breakpoint.smAndDown) {
        return this.$date.utc(day.date).format(this.$tc('global.datetime.dateShort'))
      } else {
        return this.$date.utc(day.date).format(this.$tc('global.datetime.dateLong'))
      }
    },
    weekdayFormat () {
      return ''
    },

    entryClick ({ event: entry }) {
      // if in read-only mode --> fire openEntry event
      // in edit mode, the event is already fired by drag & drop logic
      if (!this.editable) {
        this.$emit('openEntry', entry)
      }
    }

  }
}
</script>
<style lang="scss">
.ec-picasso {

  .v-calendar-daily_head-day,
  .v-calendar-daily__day {
    min-width: 80px;
  }

  .v-event-timed {
    padding: 1px;
    font-size: 11px !important;
    white-space: normal;
    line-height: 1.15;
    user-select: none;
    -webkit-user-select: none;

    .pl-1 {
      padding-left: 2px !important;
    }
  }
}

.v-current-time {
  height: 2px;
  background-color: #ea4335;
  position: absolute;
  left: -1px;
  right: 0;
  pointer-events: none;

  &::before {
    content: '';
    position: absolute;
    background-color: #ea4335;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-top: -5px;
    margin-left: -6.5px;
  }
}

.v-calendar .v-event-timed-container {
  margin-right: 5px;
}

@media #{map-get($display-breakpoints, 'sm-and-up')}{
  .ec-event--btn {
    display: block !important;
  }
}

.ec-event--btn {
  padding: 0 !important;
  min-width: 20px !important;
  top: 0 !important;
  right: 0 !important;
  display: none;
}

.v-event-title {
  hyphens: auto;
  hyphenate-limit-chars: 6 3 3;
  hyphenate-limit-lines: 2;
  hyphenate-limit-last: always;
  hyphenate-limit-zone: 8%;
}

.ec-daily_head-day-label {
  font-size: 11px;
  font-feature-settings: "tnum";
  letter-spacing: -.1px;

  .elipsis {
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
    display: block;
  }
}
</style>
<style lang="scss" scoped>
.v-card {
  overflow: hidden;
}

::v-deep .v-calendar-daily__pane {
  overflow-y: visible;
}

::v-deep .v-calendar-daily__scroll-area {
  overflow-y: visible;
}

::v-deep .v-calendar-daily {
  border-top: 0;
  border-left: 0;
  overflow-x: scroll;
}

::v-deep .v-calendar-daily__body {
  overflow: visible;
}

::v-deep .v-event-timed.v-event--temporary {
  border-style: dashed !important;
  opacity: .8;
}

@media #{map-get($display-breakpoints, 'sm-and-up')}{
  .v-event-timed {
    &:hover .v-event-drag-bottom::after {
      display: block;
    }
  }
}

.v-event-drag-bottom {
  position: absolute;
  left: 0;
  right: 0;
  bottom: 4px;
  height: 4px;
  cursor: ns-resize;

  &::after {
    display: none;
    position: absolute;
    left: 50%;
    height: 4px;
    border-top: 1px solid white;
    border-bottom: 1px solid white;
    width: 16px;
    margin-left: -8px;
    opacity: 0.8;
    content: '';
  }
}
</style>
