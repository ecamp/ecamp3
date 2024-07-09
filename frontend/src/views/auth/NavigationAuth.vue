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
      <v-btn :href="newsLink" target="_blank" text>
        <v-icon left>mdi-script-text-outline</v-icon>
        {{ $tc('global.navigation.news') }}
      </v-btn>
    </v-toolbar-items>
    <v-btn
      :href="helpLink"
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
      <span class="d-sr-only">{{ $tc('global.navigation.help') }}</span>
    </v-btn>
  </v-app-bar>
  <v-btn
    v-else
    :href="helpLink"
    target="_blank"
    fab
    x-small
    absolute
    top
    right
    class="help-button--mobile"
  >
    <v-icon>mdi-help</v-icon>
    <span class="d-sr-only">{{ $tc('global.navigation.help') }}</span>
  </v-btn>
</template>

<script>
import Logo from '@/components/navigation/Logo.vue'
import { getEnv } from '@/environment.js'
export default {
  name: 'NavigationAuth',
  components: {
    Logo,
  },
  computed: {
    helpLink() {
      return getEnv().HELP_LINK
    },
    newsLink() {
      return getEnv().NEWS_LINK
    },
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
