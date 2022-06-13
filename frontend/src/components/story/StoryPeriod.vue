<template>
  <v-expansion-panel>
    <v-expansion-panel-header>
      <h3>{{ period.description }}</h3>
    </v-expansion-panel-header>
    <v-expansion-panel-content
      v-for="day in period.days().items"
      :key="day._meta.self"
      class="e-story-day">
      <story-day :day="day" :editing="editing" />
    </v-expansion-panel-content>
  </v-expansion-panel>
</template>
<script>
import StoryDay from './StoryDay.vue'

export default {
  name: 'StoryPeriod',
  components: { StoryDay },
  props: {
    period: { type: Object, required: true },
    editing: { type: Boolean, default: false }
  }
}
</script>
