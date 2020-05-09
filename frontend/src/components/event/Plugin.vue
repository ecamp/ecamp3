<template>
  <div v-if="eventTypePlugin" class="plugin-container">
    <div v-for="eventPlugin in eventPlugins" :key="eventPlugin._meta.self">
      <event-plugin :event-plugin="eventPlugin" />
      <br>
    </div>

    <v-btn color="primary" :loading="isAdding"
           block
           @click="addEventPlugin">
      + Add another {{ pluginName }}
    </v-btn>
  </div>
</template>

<script>

import EventPlugin from './EventPlugin'

export default {
  name: 'Plugin',
  components: {
    EventPlugin
  },
  props: {
    pluginName: { type: String, required: true },
    event: { type: Object, required: true }
  },
  data () {
    return {
      isAdding: false
    }
  },
  computed: {
    eventPlugins () {
      // TODO: should we add the deleting-filter already to the store?
      return this.event.eventPlugins().items.filter(ep => !ep._meta.deleting && ep.pluginName === this.pluginName)
    },
    plugin () {
      return this.eventTypePlugin.plugin()
    },
    // try to find the EventTypePlugin of given name `pluginName`
    // otherwise returns undefined and this component should not be shown
    eventTypePlugin () {
      return this.event.eventCategory().eventType().eventTypePlugins().items.find(etp => etp.plugin().name === this.pluginName)
    }
  },
  methods: {
    async addEventPlugin () {
      this.isAdding = true
      await this.api.post('/event-plugins', {
        eventId: this.event.id,
        eventTypePluginId: this.eventTypePlugin.id // POSSIBLE ALTERNATIVE: post with pluginId of eventTypePluginId
      })
      await this.refreshEvent()
      this.isAdding = false
    },
    async refreshEvent () {
      await this.api.reload(this.event._meta.self)
    }
  }
}
</script>

<style scoped>
  .plugin-container {
    padding:5px;
    background-color: grey;
  }
</style>
