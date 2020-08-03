<!--
Listing all given activity schedule entries in a calendar view.
-->

<template>
  <div>
    <v-calendar
      v-model="value"
      class="ec-picasso"
      :events="events"
      :event-name="getActivityName | loading('Lädt…', ({ input }) => isActivityLoading(input))"
      :event-color="getActivityColor | loading('grey lighten-2', (entry) => isActivityLoading(entry))"
      :interval-height="intervalHeight"
      interval-width="46"
      :interval-format="intervalFormat"
      first-interval="5"
      interval-count="19"
      :start="start"
      :end="end"
      :locale="$i18n.locale"
      :day-format="dayFormat"
      :type="type"
      :weekday-format="weekdayFormat"
      :weekdays="[1, 2, 3, 4, 5, 6, 0]"
      color="primary"
      :event-ripple="false"
      @change="getEvents"
      @mousedown:event="entryMouseDown"
      @mouseup:event="entryMouseUp"
      @mousedown:time="timeMouseDown"
      @mousemove:time="timeMouseMove"
      @mouseup:time="timeMouseUp"
      @mouseleave.native="nativeMouseUp">
      <template #event="{event, eventParsed, timed}">
        <h4>{{ getActivityName(event) }}</h4>
        <div
          v-if="timed"
          class="v-event-drag-bottom"
          @mousedown.stop="extendBottom(event)" />
      </template>
    </v-calendar>
    <v-menu
      :value="showEntryInfo"
      :activator="selectedElement"
      :close-on-content-click="false"
      offset-x>
      <v-card>
        <h1>test</h1>
      </v-card>
    </v-menu>
  </div>
</template>
<script>
import { scheduleEntryRoute } from '@/router'

export default {
  name: 'Picasso',
  props: {
    camp: {
      type: Function,
      required: true
    },
    scheduleEntries: {
      type: Array,
      required: true
    },
    start: {
      type: Date,
      required: true
    },
    end: {
      type: Date,
      required: true
    },
    type: {
      type: String,
      required: false,
      default: 'week'
    },
    intervalHeight: {
      type: Number,
      required: false,
      default: 42
    }
  },
  data () {
    return {
      tempScheduleEntry: null,
      weekdayShort: ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'],
      localScheduleEntries: [],
      value: '',
      draggedEntry: null,
      currentEntry: null,
      draggedStartTime: null,
      currentStartTime: null,
      extendOriginal: null,
      selectedElement: null,
      isNewEntry: false,
      showEntryInfo: false
    }
  },
  computed: {
    events () {
      if (this.tempScheduleEntry) {
        return this.localScheduleEntries.concat(this.tempScheduleEntry)
      } else {
        return this.localScheduleEntries
      }
    }
  },
  mounted () {
    this.localScheduleEntries = this.scheduleEntries.map((entry) => {
      entry.start = new Date(entry.startTime).getTime()
      entry.end = new Date(entry.endTime).getTime()
      entry.timed = true
      return entry
    })
  },
  methods: {
    getActivityName (event, _) {
      if (event.tmpEvent) {
        return event.name
      } else {
        return '(' + event.number + ') ' + event.activity().activityCategory().short + ': ' + event.activity().title
      }
    },
    getActivityColor (event, _) {
      if (event.tmpEvent) {
        return 'grey'
      } else {
        return event.activity().activityCategory().color.toString()
      }
    },
    isActivityLoading (scheduleEntry) {
      if (scheduleEntry.tmpEvent) {
        return false
      } else {
        return scheduleEntry.activity()._meta.loading
      }
    },
    intervalFormat (time) {
      return this.$moment(time.date + ' ' + time.time).format(this.$t('global.moment.hourLong'))
    },
    showScheduleEntry ({ event: scheduleEntry }) {
      this.$router.push(scheduleEntryRoute(this.camp(), scheduleEntry))
    },
    dayFormat (day) {
      if (this.$vuetify.breakpoint.smAndDown) {
        return this.$moment(day.date).format(this.$t('global.moment.dateShort'))
      } else {
        return this.$moment(day.date).format(this.$t('global.moment.dateLong'))
      }
    },
    weekdayFormat (day) {
      return ''
    },
    getEvents ({ start, end }) {
      // change period or view
    },
    entryMouseDown ({ event, timed, nativeEvent }) {
      console.log('entryMouseDown')
      if (event && timed) {
        this.draggedEntry = event
        this.draggedStartTime = null
        this.extendOriginal = null
      }
    },
    createNewEntry: function (mouse) {
      this.currentStartTime = this.roundTime(mouse)
      this.currentEntry = {
        name: this.$tc('entity.activity.new'),
        start: this.currentStartTime,
        end: this.currentStartTime,
        timed: true,
        tmpEvent: true
      }
      this.tempScheduleEntry = this.currentEntry
    },
    timeMouseDown (tms) {
      console.log('timeMouseDown')
      const mouse = this.toTime(tms)

      if (this.draggedEntry && this.draggedStartTime === null) {
        const start = this.draggedEntry.start
        this.draggedStartTime = mouse - start
      } else {
        this.createNewEntry(mouse)
      }
    },
    extendBottom (event) {
      console.log('extendBottom')
      this.currentEntry = event
      this.currentStartTime = event.start
      this.extendOriginal = event.end
    },
    changeEntryTime: function (mouse) {
      const mouseRounded = this.roundTime(mouse, false)
      const min = Math.min(mouseRounded, this.currentStartTime)
      const max = Math.max(mouseRounded, this.currentStartTime)

      this.currentEntry.start = min
      this.currentEntry.end = max
    },
    moveEntryTime: function (mouse) {
      const start = this.draggedEntry.start
      const end = this.draggedEntry.end
      const duration = end - start
      const newStartTime = mouse - this.draggedStartTime
      const newStart = this.roundTime(newStartTime)
      const newEnd = newStart + duration

      this.draggedEntry.start = newStart
      this.draggedEntry.end = newEnd
    },
    timeMouseMove (tms) {
      console.log('timeMouseMove')
      const mouse = this.toTime(tms)

      if (this.draggedEntry && this.draggedStartTime !== null) {
        this.moveEntryTime(mouse)
      } else if (this.currentEntry && this.currentStartTime !== null) {
        this.changeEntryTime(mouse)
      }
    },
    entryMouseUp ({ nativeEvent }) {
      console.log('entryMouseUp')
      const open = () => {
        this.selectedElement = nativeEvent.target
        setTimeout(() => { this.showEntryInfo = true }, 10)
      }

      if (this.showEntryInfo) {
        this.showEntryInfo = false
        setTimeout(open, 10)
      } else {
        open()
      }
    },
    timeMouseUp (tms) {
      console.log('timeMouseUp')
      this.draggedStartTime = null
      this.draggedEntry = null
      this.currentEntry = null
      this.currentStartTime = null
      this.extendOriginal = null
    },
    nativeMouseUp () {
      console.log('nativeMouseUp')
      if (this.currentEntry) {
        if (this.extendOriginal) {
          this.currentEntry.end = this.extendOriginal
        }
      }

      this.currentEntry = null
      this.currentStartTime = null
      this.draggedStartTime = null
      this.draggedEntry = null
    },
    roundTime (time, down = true) {
      const roundTo = 15 // minutes
      const roundDownTime = roundTo * 60 * 1000

      return down
        ? time - time % roundDownTime
        : time + (roundDownTime - (time % roundDownTime))
    },
    toTime (tms) {
      return new Date(tms.year, tms.month - 1, tms.day, tms.hour, tms.minute).getTime()
    },
    rnd (a, b) {
      return Math.floor((b - a + 1) * Math.random()) + a
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

  ::v-deep .v-calendar-daily__scroll-area {
    overflow-y: auto;
  }

  .v-calendar-daily {
    border-top: 0;
  }

  .v-event-timed {
    user-select: none;
    -webkit-user-select: none;

    &:hover .v-event-drag-bottom::after {
      display: block;
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
