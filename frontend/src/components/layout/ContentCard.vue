<!--
Displays the content wrapped inside a card.
-->

<template>
  <v-card
    :max-width="maxWidth"
    width="100%"
    :tile="$vuetify.breakpoint.xsOnly"
    class="mx-auto"
  >
    <v-toolbar
      v-if="back || $vuetify.breakpoint.xsOnly || toolbar"
      class="ec-content-card__toolbar"
      elevation="0"
      dense
    >
      <v-toolbar-items>
        <button-back
          v-if="back || ($vuetify.breakpoint.xsOnly && !!$route.query.isDetail)"
          class="ml-n4"
        />
      </v-toolbar-items>

      <slot name="title">
        <v-toolbar-title class="font-weight-bold">
          {{ title }}
        </v-toolbar-title>
      </slot>
      <v-spacer />

      <v-skeleton-loader v-if="!loaded" type="actions" />
      <slot v-else name="title-actions" />
    </v-toolbar>

    <!-- main content -->
    <v-sheet class="ec-content-card__content fill-height">
      <v-skeleton-loader v-if="!loaded" type="article" />
      <slot v-else />
    </v-sheet>
  </v-card>
</template>

<script>
import ButtonBack from '@/components/buttons/ButtonBack.vue'

export default {
  name: 'ContentCard',
  components: {
    ButtonBack,
  },
  props: {
    loaded: { type: Boolean, required: false, default: true },
    title: { type: String, required: false, default: '' },
    toolbar: { type: Boolean, required: false, default: false },
    back: { type: Boolean, required: false, default: false },
    maxWidth: { type: String, default: '' },
  },
}
</script>

<style scoped lang="scss">
.ec-content-card__toolbar {
  @media #{map-get($display-breakpoints, 'xs-only')} {
    position: sticky;
    top: 0;
    z-index: 5;
  }
  border-bottom: 1px solid rgba(0, 0, 0, 0.12);
}
</style>
