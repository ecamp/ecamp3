<template>
  <div>
    <e-select
      v-model="optionsScheduleEntry"
      :items="scheduleEntries" />
  </div>
</template>

<script>
export default {
  name: 'Activity',
  props: {
    value: { type: Object, required: true },
    camp: { type: Object, required: true }
  },
  data () {
    return {
    }
  },
  computed: {
    options: {
      get () { return this.value },
      set (v) { this.$emit('input', v) }
    },
    optionsScheduleEntry: {
      get () {
        return {
          activity: this.options.activity,
          scheduleEntry: this.options.scheduleEntry
        }
      },
      set (val) {
        this.options.activity = val.activity
        this.options.scheduleEntry = val.scheduleEntry
      }
    },
    scheduleEntries () {
      let scheduleEntries = []

      this.camp.periods().items.forEach(p => {
        const periodScheduleEntries = p.scheduleEntries().items.map(se => ({
          value: { activity: se.activity()._meta.self, scheduleEntry: se._meta.self },
          text: '(' + se.number + ') ' + se.activity().title
        }))
        scheduleEntries = [...scheduleEntries, ...periodScheduleEntries]
      })

      return scheduleEntries
    }
  },
  defaultOptions () {
    return {
      activity: null,
      scheduleEntry: null
    }
  }
}
</script>
