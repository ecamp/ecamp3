<!--
Displays the content wrapped inside a card.
-->

<template>
  <v-sheet class="ec-content-group mt-2">
    <slot name="title">
      <component
        :is="titleTag"
        v-if="title"
        class="ec-content-group__title py-1 subtitle-1"
      >
        <v-icon v-if="icon" left size="20" class="mb-n1">{{ icon }}</v-icon>
        {{ title }}
        <v-spacer />
        <slot name="title-actions" />
      </component>
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
    maxWidth: { type: String, default: '' },
    titleTag: { type: String, default: 'h2' },
  },
}
</script>

<style scoped lang="scss">
.ec-content-group__title {
  border-bottom: 1px solid map-get($blue-grey, 'lighten-4');
  display: flex;
  align-items: baseline;
  justify-content: space-between;
}

.ec-content-group + .ec-content-group {
  margin-top: 32px !important;
}
</style>
