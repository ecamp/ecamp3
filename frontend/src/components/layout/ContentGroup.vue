<!--
Displays the content wrapped inside a card.
-->

<template>
  <v-sheet class="ec-content-group mb-8 mt-2">
    <slot name="title">
      <div v-if="title" class="ec-content-group__title py-1 subtitle-1">
        <v-icon v-if="icon" left>{{ icon }}</v-icon>
        {{ title }}
      </div>
    </slot>
    <v-skeleton-loader v-if="!loaded" type="article" />
    <slot v-else />
  </v-sheet>
</template>

<script>
export default {
  name: 'ContentGroup',
  props: {
    loaded: { type: Boolean, required: false, default: true },
    title: { type: String, required: false, default: '' },
    icon: { type: String, required: false, default: '' },
    maxWidth: { type: String, default: '' }
  }
}
</script>

<style lang="scss">
.ec-content-group__title {
  border-bottom: 1px solid map-get($blue-grey, 'lighten-4');
  display: flex;
  align-items: baseline;
  justify-content: space-between;
}
</style>
