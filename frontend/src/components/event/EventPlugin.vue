<template>
  <div class="event-plugin-container">
    <v-btn
      color="error"
      class="float-right"
      small
      @click="removeEventPlugin">
      Remove this {{ eventPlugin.pluginName }}
    </v-btn>
    <h3> {{ eventPlugin.instanceName || eventPlugin.pluginName }}</h3>
    <component :is="eventPlugin.pluginName" :event-plugin="eventPlugin" />
    <br>
  </div>
</template>

<script>

import Textarea from '@/components/event/plugins/Textarea'
import Storyboard from '@/components/event/plugins/Storyboard'
import Richtext from '@/components/event/plugins/Richtext'

export default {
  name: 'EventPlugin',
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
  methods: {
    async removeEventPlugin () {
      this.api.del(this.eventPlugin)
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
