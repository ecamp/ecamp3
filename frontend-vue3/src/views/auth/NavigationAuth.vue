<template>
  <v-app-bar
    v-if="$vuetify.display.mdAndUp"
    app
    clipped-left
    color="blue-grey-darken-4"
    dark
  >
    <logo text />
    <v-spacer />
    <v-toolbar-items>
      <v-btn :href="newsLink" target="_blank" text>
        <v-icon left>mdi-script-text-outline</v-icon>
        {{ $t('global.navigation.news') }}
      </v-btn>
    </v-toolbar-items>
    <v-btn
      :href="helpLink"
      target="_blank"
      fab
      x-small
      light
      position="absolute"
      top
      right
      class="help-button--desktop"
    >
      <v-icon>mdi-help</v-icon>
      <span class="d-sr-only">{{ $t('global.navigation.help') }}</span>
    </v-btn>
  </v-app-bar>
  <v-btn
    v-else
    :href="helpLink"
    target="_blank"
    fab
    icon="mdi-help"
    size="small"
    position="absolute"
    location="right"
    top
    right
    class="help-button--mobile ma-4"
    style="z-index: 10"
  >
    <v-icon icon="mdi-help" />
    <span class="d-sr-only">{{ $t('global.navigation.help') }}</span>
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
