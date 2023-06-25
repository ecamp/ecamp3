<template>
  <Text
    :id="`${id}-${period.id}`"
    :bookmark="{ title: $tc('print.story.title') + ': ' + period.description, fit: true }"
    class="h1"
    >{{ $tc('print.story.title') }}: {{ period.description }}</Text
  >
  <StoryDay v-for="day in days" :id="id" :period="period" :day="day" />
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import StoryDay from './StoryDay.vue'
import sortBy from 'lodash/sortBy.js'

export default {
  name: 'StoryPeriod',
  components: { StoryDay },
  extends: PdfComponent,
  props: {
    period: { type: Object, required: true },
  },
  computed: {
    days() {
      return sortBy(this.period.days().items, (day) => this.$date.utc(day.start).unix())
    },
  },
}
</script>
<pdf-style>
</pdf-style>
