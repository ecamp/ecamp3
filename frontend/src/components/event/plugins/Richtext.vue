<template>
  <div>
    {{ $options.name }} //
    I'm a RichText content component (but Textarea in the backend): {{ eventPlugin.textarea().id }} {{ eventPlugin.textarea().text }}
  </div>
</template>

<script>

export default {
  name: 'Richtext',
  props: {
    eventPlugin: { type: Object, required: true }
  }
}
</script>

<style scoped>
</style>
