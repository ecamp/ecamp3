<template>
  <div>
    <h2>{{ period.description }}</h2>
    <story-day v-for="day in period.days().items" :key="day._meta.self"
               :day="day" />
  </div>
</template>
<script>
import StoryDay from '@/components/camp/StoryDay'

export default {
  name: 'StoryPeriod',
  components: { StoryDay },
  props: {
    period: { type: Object, required: true }
  }
}
</script>
