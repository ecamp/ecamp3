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
      :close-on-click="false"
      offset-x>
      <v-card v-if="selectedEntry">
        <v-card-text>
          <e-text-field no-label v-model="selectedEntry.name"
                        class="font-weight-bold" placeholder="Aktivitätsname"
                        hide-details="auto" />
          <e-select v-model="selectedEntry.type" label="Aktivitätstyp"
                    :items="activityTypes">
            <template #item="{item, on, attrs}">
              <v-list-item :key="item.short" v-bind="attrs" v-on="on">
                <v-list-item-avatar>
                  <v-chip :color="item.color">{{ item.short }}</v-chip>
                </v-list-item-avatar>
                <v-list-item-content>
                  {{ item.text }}
                </v-list-item-content>
              </v-list-item>
            </template>
            <template #selection="{item}">
              <div class="v-select__selection">
                <span class="black--text">
                  {{ item.text }}
                </span>
                <v-chip x-small :color="item.color">{{ item.short }}</v-chip>
              </div>
            </template>
          </e-select>
          <v-row no-gutters class="my-4">
            <e-time-picker label="Start"
                           :icon="null" class="flex-full"
                           :value="toTimeString(selectedEntry.start)" />
            <e-time-picker width="100" label="Ende"
                           :icon="null" class="flex-full mt-0"
                           :value="toTimeString(selectedEntry.end)" />
          </v-row>
          <e-select label="Verantwortliche" multiple
                    chips
                    :items="[{text:'Leitende1'},{text:'Leitende2'}]" />
        </v-card-text>
        <v-card-actions>
          <v-btn text color="secondary" class="ml-auto">Abbrechen</v-btn>
          <v-btn color="success">{{ isNewEntry ? 'Erstellen' : 'Speichern' }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-menu>
  </div>
</template>
<script>
import { scheduleEntryRoute } from '@/router'
import ESelect from '@/components/form/base/ESelect'
import ETextField from '@/components/form/base/ETextField'
import ETimePicker from '@/components/form/base/ETimePicker'

export default {
  name: 'Picasso',
  components: {
    ETextField,
    ETimePicker,
    ESelect
  },
  props: {
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
      activityTypes: [{
        value: 'ls',
        disabled: false,
        text: 'Lagersport',
        short: 'LS',
        color: 'green'
      }, {
        value: 'la',
        disabled: false,
        text: 'Lageraktivität',
        short: 'LA',
        color: 'orange'
      }],
      localScheduleEntries: [],
      parsedScheduleEntries: [],
      value: '',
      draggedEntry: null,
      currentEntry: null,
      draggedStartTime: null,
      currentStartTime: null,
      extendOriginal: null,
      selectedElement: null,
      selectedEntry: null,
      isNewEntry: false,
      isDirty: false,
      showEntryInfo: false
    }
  },
  computed: {
    events () {
      if (this.tempScheduleEntry) {
        return this.parsedScheduleEntries.concat(this.tempScheduleEntry)
      } else {
        return this.parsedScheduleEntries
      }
    }
  },
  watch: {
    scheduleEntries: function (newValue, oldValue) {
      if (!this.isDirty && this.localScheduleEntries === oldValue) {
        this.parsedScheduleEntries = newValue.map((entry) => {
          entry.name = entry.activity().title
          entry.start = new Date(entry.startTime)
          entry.end = new Date(entry.endTime)
          entry.timed = true
          return entry
        })
        this.localScheduleEntries = newValue
      }
    }
  },
  created () {
    this.parsedScheduleEntries = this.scheduleEntries.map((entry) => {
      entry.start = new Date(entry.startTime)
      entry.end = new Date(entry.endTime)
      entry.timed = true
      return entry
    })
    this.localScheduleEntries = this.scheduleEntries
  },
  methods: {
    getActivityName (event, _) {
      if (event.tmpEvent) {
        return (event.type ? this.getActivityType(event.type).short + ': ' : '') + event.name
      } else {
        return '(' + event.number + ') ' + event.activity().activityCategory().short + ': ' + event.activity().title
      }
    },
    getActivityColor (event, _) {
      if (event.tmpEvent) {
        if (event.type) {
          return this.getActivityType(event.type).color
        } else {
          return 'grey'
        }
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
    getActivityType (value) {
      return this.activityTypes.find(type => type.value === value)
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
      this.isNewEntry = false
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
        start: new Date(this.currentStartTime),
        end: new Date(this.currentStartTime),
        type: null,
        timed: true,
        tmpEvent: true
      }
      this.isNewEntry = true
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

      this.currentEntry.start = new Date(min)
      this.currentEntry.end = new Date(max)
    },
    moveEntryTime: function (mouse) {
      const start = this.draggedEntry.start
      const end = this.draggedEntry.end
      const duration = end - start
      const newStartTime = mouse - this.draggedStartTime
      const newStart = this.roundTime(newStartTime)
      const newEnd = newStart + duration

      this.draggedEntry.start = new Date(newStart)
      this.draggedEntry.end = new Date(newEnd)
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
      this.selectedElement = nativeEvent.target
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
    showEntryInfoPopup (entry) {
      this.selectedEntry = entry

      const open = () => {
        setTimeout(() => {
          this.showEntryInfo = true
        }, 10)
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
      this.isDirty = true

      if (this.draggedEntry && this.draggedStartTime === null) {
        const end = this.toTime(tms) - this.draggedEntry.start
        if ((end - this.draggedStartTime) > 60) {
          this.showEntryInfoPopup(this.draggedEntry)
        }
      } else if (this.isNewEntry) {
        this.showEntryInfoPopup(this.currentEntry)
      }

      this.clearCurrentEntry()
      this.clearDraggedEntry()
    },
    nativeMouseUp () {
      console.log('nativeMouseUp')
      if (this.currentEntry) {
        if (this.extendOriginal) {
          this.currentEntry.end = this.extendOriginal
        }
      }

      this.clearCurrentEntry()
      this.clearDraggedEntry()
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
