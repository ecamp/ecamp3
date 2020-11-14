<template>
  <div>
    <h4>
      {{ period.description }}
    </h4>
    <template v-for="(scheduleEntries, uri) in scheduleEntriesGroupedByDay">
      <story-day v-if="scheduleEntries.length"
                 :key="uri"
                 :schedule-entries="scheduleEntries" />
    </template>
  </div>
</template>
<script>
import { groupBy, sortBy } from 'lodash'
import StoryDay from '@/components/camp/StoryDay'

export default {
  name: 'StoryPeriod',
  components: { StoryDay },
  props: {
    period: { type: Object, required: true }
  },
  computed: {
    scheduleEntriesGroupedByDay () {
      return groupBy(
        sortBy(
          this.period.scheduleEntries().items,
          scheduleEntry => scheduleEntry.periodOffset
        ),
        scheduleEntry => scheduleEntry.day()._meta.self
      )
    }
  }
}
</script>
