<!--
Displays the content wrapped inside a card.
-->

<template>
  <v-card :max-width="maxWidth" :tile="$vuetify.breakpoint.xsOnly" class="mx-auto">
    <v-toolbar v-if="back || $vuetify.breakpoint.xsOnly || toolbar" class="ec-content-card__toolbar"
               elevation="0"
               dense>
      <v-toolbar-items>
        <button-back v-if="back || $vuetify.breakpoint.xsOnly && !!$route.query.isDetail" class="ml-n4" />
      </v-toolbar-items>
      <slot name="title">
        <v-toolbar-title>
          {{ title }}
        </v-toolbar-title>
      </slot>
      <v-spacer />
      <slot name="title-actions" />
    </v-toolbar>
    <v-divider v-if="$vuetify.breakpoint.xsOnly || toolbar" />
    <v-sheet class="ec-content-card__content fill-height">
      <v-skeleton-loader v-if="!loaded" type="article" />
      <slot v-else />
    </v-sheet>
  </v-card>
</template>

<script>
import ButtonBack from '@/components/buttons/ButtonBack'

export default {
  name: 'ContentCard',
  components: {
    ButtonBack
  },
  props: {
    loaded: { type: Boolean, required: false, default: true },
    title: { type: String, required: false, default: '' },
    toolbar: { type: Boolean, required: false, default: false },
    back: { type: Boolean, required: false, default: false },
    maxWidth: { type: String, default: '' }
  }
}
</script>

<style scoped lang="scss">
@import '~vuetify/src/styles/settings/variables';
@import '~vuetify/src/styles/settings/colors';

.v-toolbar__title {
  font-weight: 600;
}
</style>
