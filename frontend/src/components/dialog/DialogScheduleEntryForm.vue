<template>
  <div>
    <v-row>
      <v-col cols="12">
        <!-- Period -->
        <e-select
          v-model="localScheduleEntry.periodId"
          :label="$tc('entity.period.name')"
          :items="periodOptions"
          vee-rules="required" />
      </v-col>
    </v-row>

    <!-- Start -->
    <v-row>
      <v-col cols="6">
        <e-select
          v-model="startDayOffset"
          :label="$tc('components.dialog.dialogScheduleEntryForm.startDate')"
          :items="dayOptions"
          vee-rules="required" />
      </v-col>
      <v-col cols="6">
        <e-time-picker
          v-model="startTime"
          :label="$tc('components.dialog.dialogScheduleEntryForm.startTime')"
          vee-rules="required" />
      </v-col>
    </v-row>

    <!-- End -->
    <v-row>
      <v-col cols="6">
        <e-select
          v-model="endDayOffset"
          :label="$tc('components.dialog.dialogScheduleEntryForm.endDate')"
          :items="dayOptions"
          vee-rules="required" />
      </v-col>
      <v-col cols="6">
        <e-time-picker
          v-model="endTime"
          :label="$tc('components.dialog.dialogScheduleEntryForm.endTime')"
          vee-rules="required" />
      </v-col>
    </v-row>
  </div>
</template>

<script>
export default {
  name: 'DialogScheduleEntryForm',
  props: {
    scheduleEntry: { type: Object, required: true },
    camp: { type: Function, required: true }
  },
  data () {
    return {
      localScheduleEntry: this.scheduleEntry
    }
  },
  computed: {
    scheduleEntryStart: {
      get () { return this.localScheduleEntry.periodOffset },
      set (start) {
        this.localScheduleEntry.length = this.scheduleEntryEnd - start
        this.localScheduleEntry.periodOffset = start
      }
    },
    scheduleEntryEnd: {
      get () { return this.scheduleEntryStart + this.localScheduleEntry.length },
      set (end) { this.localScheduleEntry.length = end - this.scheduleEntryStart }
    },

    startDayOffset: {
      get () { return Math.floor(this.scheduleEntryStart / (24 * 60)) },
      set (day) { this.scheduleEntryStart = day * (24 * 60) + this.startMinOffset }
    },
    startMinOffset: {
      get () { return this.scheduleEntryStart % (24 * 60) },
      set (min) { this.scheduleEntryStart = this.startDayOffset * (24 * 60) + min }
    },
    startTime: {
      get () { return '2000-01-01T' + Math.floor(this.startMinOffset / 60) + ':' + (this.startMinOffset % 60) + ':00+00:00' },
      set (time) { const d = this.$date.utc(time); this.startMinOffset = 60 * d.$H + d.$m }
    },

    endDayOffset: {
      get () { return Math.floor(this.scheduleEntryEnd / (24 * 60)) },
      set (day) { this.scheduleEntryEnd = day * (24 * 60) + this.endMinOffset }
    },
    endMinOffset: {
      get () { return this.scheduleEntryEnd % (24 * 60) },
      set (min) { this.scheduleEntryEnd = this.endDayOffset * (24 * 60) + min }
    },
    endTime: {
      get () { return '2000-01-01T' + Math.floor(this.endMinOffset / 60) + ':' + (this.endMinOffset % 60) + ':00+00:00' },
      set (time) { const d = this.$date.utc(time); this.endMinOffset = 60 * d.$H + d.$m }
    },

    period () {
      return this.camp().periods().items.find(p => p.id === this.localScheduleEntry.periodId)
    },
    periodOptions () {
      return this.camp().periods().items.map(
        p => ({
          value: p.id,
          text: p.description
        })
      )
    },
    dayOptions () {
      if (this.period) {
        return this.period.days().items.map(d => ({
          value: d.dayOffset,
          text: '(' + d.number + ') ' + this.dayDate(this.period, d)
        }))
      } else {
        return []
      }
    }
  },
  methods: {
    dayDate (period, day) {
      return this.$date.utc(period.start)
        .add(day.dayOffset, 'd')
        .format(this.$tc('global.datetime.dateLong'))
    }
  }
}
</script>
