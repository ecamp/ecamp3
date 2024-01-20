<template>
  <v-app-bar
    v-if="$vuetify.breakpoint.mdAndUp"
    app
    clipped-left
    color="blue-grey darken-4"
    dark
  >
    <logo text />
    <v-spacer />
    <v-toolbar-items>
      <v-btn href="https://ecamp3.ch/blog" target="_blank" text>
        <v-icon left>mdi-script-text-outline</v-icon>
        {{ $tc('global.buttons.news') }}
      </v-btn>
    </v-toolbar-items>
    <v-btn
      href="https://ecamp3.ch/faq"
      target="_blank"
      fab
      x-small
      light
      absolute
      top
      right
      class="help-button--desktop"
    >
      <v-icon>mdi-help</v-icon>
      <span class="d-sr-only">{{ $tc('global.buttons.help') }}</span>
    </v-btn>
  </v-app-bar>
  <v-btn
    v-else
    href="https://ecamp3.ch/faq"
    target="_blank"
    fab
    x-small
    absolute
    top
    right
    class="help-button--mobile"
  >
    <v-icon>mdi-help</v-icon>
    <span class="d-sr-only">{{ $tc('global.buttons.help') }}</span>
  </v-btn>
</template>

<script>
import Logo from '@/components/navigation/Logo.vue'

export default {
  name: 'NavigationAuth',
  components: {
    Logo,
  },
}
</script>

<style scoped>
.help-button--desktop {
  top: 80px !important;
}
.help-button--mobile {
  top: 16px !important;
}
</style>
