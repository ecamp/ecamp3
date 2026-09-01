<!--
Displays the content wrapped inside a card.
-->

<template>
  <v-card :max-width="maxWidth" width="100%" :tile="$vuetify.display.xs" class="mx-auto">
    <v-toolbar
      v-if="back || !$vuetify.display.mdAndUp || toolbar"
      class="ec-content-card__toolbar max-w-screen"
      :class="{ 'ec-content-card__toolbar--border': !noBorder }"
      elevation="0"
      color="surface"
      density="compact"
    >
      <v-toolbar-items>
        <button-back v-if="showBackButton" :to="backTarget" />
      </v-toolbar-items>

      <slot name="title">
        <v-toolbar-title
          tag="h1"
          class="font-weight-bold flex-none"
          :class="{ 'ml-0': showBackButton }"
        >
          {{ title }}
        </v-toolbar-title>
      </slot>
      <v-spacer />

      <template v-if="!loaded" #append>
        <v-skeleton-loader type="button" width="40" class="mr-2" />
      </template>
      <slot v-if="loaded" name="title-actions" />
    </v-toolbar>

    <!-- main content -->
    <v-sheet class="ec-content-card__content fill-height">
      <v-skeleton-loader v-if="!loaded" type="article" class="pa-4" />
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
    noBorder: { type: Boolean, required: false, default: false },
    back: { type: [Boolean, String, Object], default: false },
    maxWidth: { type: String, default: '' },
  },
  computed: {
    backTarget() {
      const target = this.back || this.$route?.meta?.back
      return typeof target === 'object' || typeof target === 'string' ? target : null
    },
    showBackButton() {
      return (
        !!this.back ||
        !!this.$route?.meta?.back ||
        (!!this.$route?.meta?.backMobile && !this.$vuetify.display.mdAndUp)
      )
    },
  },
}
</script>

<style scoped lang="scss">
@use 'vuetify/settings';
@use 'sass:map';

.ec-content-card__toolbar {
  @media #{map.get(settings.$display-breakpoints, 'xs')} {
    position: sticky;
    top: 0;
    z-index: 5;
  }
}

:deep(.ec-content-card__toolbar:not(:hover) button.visible-on-hover:not(:focus)) {
  opacity: 0;
}

:deep(.ec-content-card__toolbar button.visible-on-hover) {
  opacity: 1;
  transition: opacity 0.2s linear;
}

.ec-content-card__toolbar--border {
  border-bottom: 1px solid rgba(var(--v-border-color), 0.12) !important;
}
</style>
