<!--
Displays the content wrapped inside a card.
-->

<template>
  <v-card class="mb-4">
    <v-toolbar dense color="blue-grey lighten-5">
      <v-icon left>
        {{ icon }}
      </v-icon>
      <v-toolbar-title>
        {{ title }}
      </v-toolbar-title>
    </v-toolbar>
    <v-card-text>
      <slot />
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  name: 'ContentCard',
  props: {
    icon: { type: String, required: true },
    title: { type: String, required: true }
  },
  data () {
    return {
    }
  }
}
</script>

<style scoped>
</style>
