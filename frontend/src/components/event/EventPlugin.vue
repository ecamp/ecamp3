<template>
  <div class="event-plugin-container">
    <v-btn
      color="error"
      class="float-right"
      :loading="isDeleting"
      @click="removeEventPlugin">
      Remove this plugin
    </v-btn>

    {{ $options.name }} //
    I'm one specific instance of the {{ eventPlugin.plugin_name }} plugin...
    <component :is="eventPlugin.plugin_name" :event-plugin="eventPlugin" />
    <br>
  </div>
</template>

<script>

import Textarea from '@/components/event/plugins/Textarea'
import Storyboard from '@/components/event/plugins/Storyboard'
import Richtext from '@/components/event/plugins/Richtext'

export default {
  name: 'EventTypePlugin',
  components: {
    Textarea,
    Storyboard,
    Richtext
  },
  props: {
    eventPlugin: { type: Object, required: true }
  },
  data () {
    return {
      isDeleting: false
    }
  },
  computed: {
    eventPlugins () {
      return this.event.event_plugins().items.filter(ep => ep.event_type_plugin().id === this.eventTypePlugin.id)
    }
  },
  methods: {
    async removeEventPlugin () {
      this.isDeleting = true
      this.api.del(this.eventPlugin)
      this.refreshEvent()
      this.isDeleting = false
    },
    async refreshEvent () {
      await this.api.reload(this.eventPlugin.event()._meta.self)
    }
  }
}
</script>

<style scoped>
  .event-plugin-container {
    border: 1px grey dashed;
    padding:5px;
    background-color:lightgrey;
  }
</style>
