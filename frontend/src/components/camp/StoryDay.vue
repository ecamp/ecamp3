<template>
  <div>
    <h5>
      {{ dayName }}
    </h5>
    <template v-for="scheduleEntry in scheduleEntries">
      <story-activity :key="scheduleEntry._meta.self" :activity="scheduleEntry.activity()" />
    </template>
  </div>
</template>
<script>
import StoryActivity from '@/components/camp/StoryActivity'

export default {
  name: 'StoryDay',
  components: { StoryActivity },
  props: {
    scheduleEntries: { type: Array, required: true }
  },
  computed: {
    dayName () {
      const firstScheduleEntry = this.scheduleEntries[0]
      const date = this.addDays(firstScheduleEntry.period().start, firstScheduleEntry.day().dayOffset)
      return this.$moment.utc(date).format(this.$tc('global.moment.dateLong'))
    }
  },
  methods: {
    addDays (date, days) {
      return Date.parse(date) + days * 24 * 60 * 60 * 1000
    }
  }
}
</script>
