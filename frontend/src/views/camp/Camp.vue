<!--
Displays several tabs with details on a single camp.
-->

<template>
  <v-container fluid :style="layoutStyle">
    <router-view v-bind="$attrs" />
  </v-container>
</template>

<script>
export default {
  name: 'Camp',
  props: {
    layout: { type: String, required: false, default: 'full' },
  },
  computed: {
    layoutStyle() {
      switch (this.layout) {
        case 'normal':
          return { maxWidth: '900px' }
        case 'wide':
          return { maxWidth: '1400px' }
        default:
          return null
      }
    },
  },
}
</script>
