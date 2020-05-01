<template>
  <div>
    {{ $options.name }} //
    I'm a Storboard content component:
    <div v-for="section in eventPlugin.sections().items" :key="section._meta.self">
      {{ section.id }} {{ section.text }}
    </div>
  </div>
</template>

<script>

export default {
  name: 'Storyboard',
  props: {
    eventPlugin: { type: Object, required: true }
  }
}
</script>

<style scoped>
</style>
