<template>
  <div>
    <e-select
      v-model="optionsActivity"
      :items="activities" />
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
    optionsActivity: {
      get () { return this.options.activity },
      set (a) {
        this.options.activity = a
        this.options.scheduleEntry = null
      }
    },
    optionsScheduleEntry: {
      get () { return this.options.scheduleEntry },
      set (s) { this.options.scheduleEntry = s }
    },
    activities () {
      return this.camp.activities().items.map(a => ({
        value: a._meta.self,
        text: a.title
      }))
    },
    scheduleEntries () {
      if (this.options.activity != null) {
        const activity = this.api.get(this.options.activity)
        const scheduleEntries = activity.scheduleEntries().items
        return scheduleEntries.map(se => ({
          value: se._meta.self,
          text: se.number
        }))
      }
      return []
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
