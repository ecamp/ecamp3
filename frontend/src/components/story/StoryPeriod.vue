<template>
  <v-expansion-panel>
    <v-expansion-panel-header>
      <h3>{{ period.description }}</h3>
    </v-expansion-panel-header>
    <story-day v-for="day in period.days().items" :key="day._meta.self"
               :day="day"
               :editing="editing" />
  </v-expansion-panel>
</template>
<script>
import StoryDay from '@/components/story/StoryDay.vue'

export default {
  name: 'StoryPeriod',
  components: { StoryDay },
  props: {
    period: { type: Object, required: true },
    editing: { type: Boolean, default: false }
  }
}
</script>
