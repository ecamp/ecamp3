<template>
  <div>
    {{ $options.name }} //
    I'm a TextArea content component: {{ eventPlugin.textarea().id }} {{ eventPlugin.textarea().text }}
  </div>
</template>

<script>

export default {
  name: 'Textarea',
  props: {
    eventPlugin: { type: Object, required: true }
  }
}
</script>

<style scoped>
</style>
