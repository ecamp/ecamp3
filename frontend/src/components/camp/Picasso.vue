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
      :events="events"
      :event-name="getActivityName | loading('Lädt…', ({ input }) => isActivityLoading(input))"
      :event-color="getActivityColor | loading('grey lighten-2', (entry) => isActivityLoading(entry))"
      event-start="startTime"
      event-end="endTime"
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
      :max-days="maxDays"
      :weekday-format="weekdayFormat"
      :weekdays="[1, 2, 3, 4, 5, 6, 0]"
      color="primary"
      :event-ripple="false"
      @mousedown:event="entryMouseDown"
      @mouseup:event="entryMouseUp"
      @mousedown:time="timeMouseDown"
      @mousemove:time="timeMouseMove"
      @mouseup:time="timeMouseUp"
      @mouseleave.native="nativeMouseUp">
      <template #day-label-header="time">
        <div class="ec-daily_head-day-label">
          <span v-if="widthPluralization > 0" class="d-block">
            {{ $moment(time.date).format('dddd') }}
          </span> {{ $moment(time.date).format($tc('components.camp.picasso.moment.date', widthPluralization)) }}
        </div>
      </template>
      <template #day-body="{ date }">
        <div v-if="dateNow === date"
             class="v-current-time" :style="{ top: nowY }" />
      </template>
      <template #event="{event, eventParsed, timed}">
        <v-btn v-if="!event.tmpEvent" absolute
               top
               right x-small
               dark text
               class="ec-event--btn rounded-sm"
               @click.stop="showEntryInfoPopup(event)">
          <v-icon x-small>mdi-pencil</v-icon>
        </v-btn>
        <h4>{{ getActivityName(event) }}</h4>
        <div
          v-if="timed"
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

    <dialog-activity-create
      ref="dialogActivityCreate"
      :schedule-entry="popupEntry" @activityCreated="tempScheduleEntry = null" />
    <dialog-activity-edit
      ref="dialogActivityEdit"
      :schedule-entry="popupEntry" />
  </div>
</template>
<script>
import { scheduleEntryRoute } from '@/router'
import { isCssColor } from 'vuetify/lib/util/colorUtils'
import DialogActivityCreate from '@/components/dialog/DialogActivityCreate'
import DialogActivityEdit from '@/components/dialog/DialogActivityEdit'

export default {
  name: 'Picasso',
  components: {
    DialogActivityCreate,
    DialogActivityEdit
  },
  props: {
    period: {
      type: Function,
      required: true
    },
    start: {
      type: Number,
      required: true
    },
    end: {
      type: Number,
      required: true
    },
    type: {
      type: String,
      required: false,
      default: 'custom-daily'
    },
    intervalHeight: {
      type: Number,
      required: false,
      default: 42
    }
  },
  data () {
    return {
      scheduleEntries: [],
      tempScheduleEntry: {},
      popupEntry: {},
      maxDays: 100,
      entryWidth: 80,
      value: '',
      isSaving: false,
      patchError: false,
      draggedEntry: null,
      currentEntry: null,
      mouseStartTime: null,
      draggedStartTime: null,
      currentStartTime: null,
      extendOriginal: null,
      nativeTarget: null
    }
  },
  computed: {
    events () {
      if (this.tempScheduleEntry && this.tempScheduleEntry.tmpEvent) {
        return this.scheduleEntries.concat(this.tempScheduleEntry)
      } else {
        return this.scheduleEntries
      }
    },
    activityCategories () {
      return this.camp.activityCategories()
    },
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
    apiScheduleEntries () {
      return this.period().scheduleEntries()
    },
    camp () {
      return this.period().camp()
    }
  },
  watch: {
    apiScheduleEntries (value) {
      this.scheduleEntries = value.items.map((entry) => {
        return {
          ...entry,
          timed: true,
          get startTime () {
            return this.period().start + (this.periodOffset * 60000)
          },
          set startTime (value) {
            this.periodOffset = (value - this.period().start) / 60000
          },
          get endTime () {
            return this.startTime + (this.length * 60000)
          },
          set endTime (value) {
            this.length = (value - this.startTime) / 60000
          }
        }
      })
    }
  },
  methods: {
    resize () {
      const widthIntervals = 46
      this.entryWidth = Math.max((this.$refs.calendar.$el.offsetWidth - widthIntervals) / this.$refs.calendar.days.length, 80)
    },
    getActivityCategory (scheduleEntry, _) {
      return scheduleEntry.activity().activityCategory()
    },
    getActivityName (scheduleEntry, _) {
      return (scheduleEntry.number ? '(' + scheduleEntry.number + ') ' : '') +
        (scheduleEntry.activity().activityCategory().short ? scheduleEntry.activity().activityCategory().short + ': ' : '') +
        scheduleEntry.activity().title
    },
    getActivityColor (scheduleEntry, _) {
      const color = scheduleEntry.activity().activityCategory().color
      return isCssColor(color) ? color : color + ' elevation-4 v-event--temporary'
    },
    isActivityLoading (scheduleEntry) {
      return scheduleEntry.activity()._meta ? scheduleEntry.activity()._meta.loading : false
    },
    intervalFormat (time) {
      return this.$moment(time.date + ' ' + time.time).format(this.$tc('global.moment.hourLong'))
    },
    dayFormat (day) {
      if (this.$vuetify.breakpoint.smAndDown) {
        return this.$moment(day.date).format(this.$tc('global.moment.dateShort'))
      } else {
        return this.$moment(day.date).format(this.$tc('global.moment.dateLong'))
      }
    },
    scheduleEntryRoute,
    showScheduleEntry (entry) {
      this.$router.push(scheduleEntryRoute(this.camp, entry))
    },
    showScheduleEntryInNewTab (entry) {
      const routeData = this.$router.resolve(scheduleEntryRoute(this.camp, entry))
      window.open(routeData.href, '_blank')
    },
    weekdayFormat () {
      return ''
    },
    entryMouseDown ({ event: entry, timed, nativeEvent }) {
      if (!entry.tmpEvent && nativeEvent.detail === 2) {
        // Doubleclick opens activity
        this.showScheduleEntry(entry)
      } else if (!entry.tmpEvent && (nativeEvent.button === 1 || nativeEvent.metaKey || nativeEvent.ctrlKey)) {
        // Click with middle mouse button, or click while holding cmd/ctrl opens new tab
        this.showScheduleEntryInNewTab(entry)
        this.openEntry = true
      } else if (this.openEntry) {

      } else {
        if (entry && timed) {
          this.draggedEntry = entry
          this.draggedStartTime = null
          this.extendOriginal = null
        }
      }
    },
    timeMouseDown (tms) {
      const mouse = this.toTime(tms)

      if (this.mouseStartTime === null) {
        this.mouseStartTime = mouse
      }

      if (this.draggedEntry && this.draggedStartTime === null) {
        const start = this.draggedEntry.startTime
        this.draggedStartTime = mouse - start
      } else {
        this.createNewEntry(mouse)
      }
    },
    createNewEntry: function (mouse) {
      this.currentStartTime = this.roundTimeDown(mouse)
      this.currentEntry = {
        number: null,
        period: (this.period)(),
        periodOffset: 0,
        length: 0,
        get startTime () {
          return this.period.start + (this.periodOffset * 60000)
        },
        set startTime (value) {
          this.periodOffset = (value - this.period.start) / 60000
        },
        get endTime () {
          return this.startTime + (this.length * 60000)
        },
        set endTime (value) {
          this.length = (value - this.startTime) / 60000
        },
        activity: () => ({
          title: this.$tc('entity.activity.new'),
          location: '',
          camp: (this.period)().camp,
          activityCategory: () => ({
            id: null,
            short: null,
            color: 'grey elevation-4 v-event--temporary'
          })
        }),
        timed: true,
        tmpEvent: true
      }
      this.currentEntry.startTime = this.currentStartTime
      this.currentEntry.endTime = this.currentStartTime
      this.tempScheduleEntry = this.currentEntry
    },
    timeMouseMove (tms) {
      const mouse = this.toTime(tms)

      if (this.draggedEntry && this.draggedStartTime !== null) {
        this.moveEntryTime(mouse)
      } else if (this.currentEntry && this.currentStartTime !== null) {
        this.changeEntryTime(mouse)
      }
    },
    changeEntryTime: function (mouse) {
      const mouseRounded = this.roundTimeUp(mouse)
      const min = Math.min(mouseRounded, this.currentStartTime)
      const max = Math.max(mouseRounded, this.currentStartTime)

      this.currentEntry.startTime = min
      this.currentEntry.endTime = max
    },
    moveEntryTime: function (mouse) {
      const start = this.draggedEntry.startTime
      const end = this.draggedEntry.endTime
      const duration = end - start
      const newStartTime = mouse - this.draggedStartTime
      const newStart = this.roundTimeDown(newStartTime)
      const newEnd = newStart + duration

      this.draggedEntry.startTime = newStart
      this.draggedEntry.endTime = newEnd
    },
    entryMouseUp ({ nativeEvent }) {
      if ((this.draggedEntry && this.draggedStartTime !== null) || (this.currentEntry && this.currentStartTime !== null)) {
        this.nativeTarget = nativeEvent.target
      }
    },
    timeMouseUp (tms) {
      if (this.draggedEntry && this.draggedStartTime !== null) {
        const minuteThreshold = 15
        const threshold = minuteThreshold * 60 * 1000
        const now = this.toTime(tms)
        if (Math.abs(now - this.mouseStartTime) < threshold) {
          this.showEntryInfoPopup(this.draggedEntry)
        } else if (!this.draggedEntry.tmpEvent) {
          const patchedScheduleEntry = {
            periodOffset: this.draggedEntry.periodOffset,
            length: this.draggedEntry.length
          }
          this.isSaving = true
          this.api.patch(this.draggedEntry._meta.self, patchedScheduleEntry).then(() => {
            this.patchError = false
            this.isSaving = false
          }).catch((error) => {
            this.patchError = error
          })
        }
        this.clearDraggedEntry()
      } else if (this.currentEntry && this.currentStartTime !== null) {
        if (this.currentEntry.tmpEvent) {
          if (!this.extendOriginal) {
            this.showEntryInfoPopup(this.currentEntry)
          }
        } else {
          const patchedScheduleEntry = {
            periodOffset: this.currentEntry.periodOffset,
            length: this.currentEntry.length
          }
          this.isSaving = true
          this.api.patch(this.currentEntry._meta.self, patchedScheduleEntry).then(() => {
            this.patchError = false
            this.isSaving = false
          }).catch((error) => {
            this.patchError = error
          })
        }
        this.clearCurrentEntry()
      }
      this.mouseStartTime = null
    },
    nativeMouseUp () {
      if (this.currentEntry) {
        if (this.extendOriginal) {
          this.currentEntry.endTime = this.extendOriginal
        }
      }
      this.clearDraggedEntry()
      this.clearCurrentEntry()
    },
    extendBottom (event) {
      this.currentEntry = event
      this.currentStartTime = event.startTime
      this.extendOriginal = event.endTime
    },
    clearCurrentEntry () {
      this.currentEntry = null
      this.currentStartTime = null
      this.extendOriginal = null
    },
    clearDraggedEntry () {
      this.draggedStartTime = null
      this.draggedEntry = null
    },
    clearEntry () {
      this.clearCurrentEntry()
      this.clearDraggedEntry()
    },
    showEntryInfoPopup (entry) {
      this.popupEntry = entry
      this.$nextTick().then(() => {
        if (entry._meta) {
          this.$refs.dialogActivityEdit.showDialog = true
        } else {
          this.$refs.dialogActivityCreate.showDialog = true
        }
      })
    },
    roundTimeDown (time) {
      const roundTo = 15 // minutes
      const roundDownTime = roundTo * 60 * 1000

      return time - time % roundDownTime
    },
    roundTimeUp (time) {
      const roundTo = 15 // minutes
      const roundDownTime = roundTo * 60 * 1000

      return time + (roundDownTime - (time % roundDownTime))
    },
    toTime (tms) {
      return new Date(tms.year, tms.month - 1, tms.day, tms.hour, tms.minute).getTime()
    },
    toTimeString (date) {
      return this.$moment(date).format(this.$tc('global.moment.hourLong'))
    },
    rnd (a, b) {
      return Math.floor((b - a + 1) * Math.random()) + a
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

.ec-event--btn {
  padding: 0 !important;
  min-width: 20px !important;
  top: 0 !important;
  right: 0 !important;
  opacity: 0;
}

.ec-daily_head-day-label {
  font-size: 11px;
  font-feature-settings: "tnum";
  letter-spacing: -.1px;
  white-space: break-spaces;

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

.v-event-timed {
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
