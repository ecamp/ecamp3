<template>
  <div class="plugin-container">
    {{ $options.name }} //
    I'm the {{ eventTypePlugin.plugin().name }} plugin. I exist once per event.
    <br><br>

    <div v-for="eventPlugin in eventPlugins" :key="eventPlugin._meta.self">
      I'm one specific instance of the {{ eventPlugin.plugin_name }} plugin...
      <component :is="eventPlugin.plugin_name" :event-plugin="eventPlugin" />
      <br>
    </div>
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
    eventTypePlugin: { type: Function, required: true },
    eventPlugins: { type: Array, required: true }
  }
}
</script>

<style scoped>
  .plugin-container {
    border: 2px grey dashed;
    padding:5px;
  }
</style>
