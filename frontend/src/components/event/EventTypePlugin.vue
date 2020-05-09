<template>
  <div class="plugin-container">
    {{ $options.name }} //
    I'm the {{ eventTypePlugin.plugin().name }} plugin. I exist once per event.
    <br><br>

    <div v-for="eventPlugin in eventPlugins" :key="eventPlugin._meta.self">
      <event-plugin :event-plugin="eventPlugin" />
      <br>
    </div>

    <v-btn color="primary" :loading="isAdding"
           block
           @click="addEventPlugin">
      + Add another {{ eventTypePlugin.plugin().name }}
    </v-btn>
  </div>
</template>

<script>

import EventPlugin from './EventPlugin'

export default {
  name: 'EventTypePlugin',
  components: {
    EventPlugin
  },
  props: {
    eventTypePlugin: { type: Object, required: true },
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
      return this.event.event_plugins().items.filter(ep => !ep._meta.deleting && ep.plugin().id === this.eventTypePlugin.plugin().id)
    }
  },
  methods: {
    async addEventPlugin () {
      this.isAdding = true
      await this.api.post('/event-plugins', {
        event_id: this.event.id,
        event_type_plugin_id: this.eventTypePlugin.id // POSSIBLE ALTERNATIVE: post with plugin_id of event_type_plugin_id
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
