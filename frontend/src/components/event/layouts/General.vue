<!--
Displays a single event
-->

<template>
  <div>
    <v-expansion-panels
      v-model="panel"
      multiple>
      <v-expansion-panel>
        <v-expansion-panel-header>Header</v-expansion-panel-header>
        <v-expansion-panel-content>
          <plugin
            plugin-name="Textarea"
            :event="event" />
        </v-expansion-panel-content>
      </v-expansion-panel>

      <v-expansion-panel>
        <v-expansion-panel-header>Storyboards</v-expansion-panel-header>
        <v-expansion-panel-content>
          <plugin
            plugin-name="Storyboard"
            :event="event" />
        </v-expansion-panel-content>
      </v-expansion-panel>

      <v-expansion-panel>
        <v-expansion-panel-header>Footer</v-expansion-panel-header>
        <v-expansion-panel-content>
          <plugin
            plugin-name="Richtext"
            :event="event" />
        </v-expansion-panel-content>
      </v-expansion-panel>
    </v-expansion-panels>
  </div>
</template>

<script>

import Plugin from '@/components/event/Plugin'

export default {
  name: 'General',
  components: {
    Plugin
  },
  props: {
    event: { type: Object, required: true }
  },
  data () {
    return {
      panel: [1, 2, 3]
    }
  },
  computed: {
    eventType () {
      return this.event.eventCategory().eventType()
    },
    eventTypePlugins () {
      return this.eventType.eventTypePlugins()
    }
  }
}
</script>

<style scoped>
</style>
