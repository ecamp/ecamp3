<!--
Displays the content wrapped inside a card.
-->

<template>
  <v-card :max-width="maxWidth" :tile="$vuetify.breakpoint.xsOnly" class="mx-auto">
    <slot name="title">
      <v-card-title v-if="title">
        <v-icon v-if="icon" left>{{ icon }}</v-icon>
        {{ title }}
      </v-card-title>
    </slot>
    <v-skeleton-loader v-if="!loaded" type="article" />
    <slot v-else />
  </v-card>
</template>

<script>
export default {
  name: 'ContentCard',
  props: {
    loaded: { type: Boolean, required: false, default: true },
    title: { type: String, required: false, default: '' },
    icon: { type: String, required: false, default: '' },
    maxWidth: { type: String, default: '' }
  }
}
</script>

<style scoped>
  .v-card__title {font-weight: 600;}
</style>
