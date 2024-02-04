<template>
  <v-app-bar
    v-if="$vuetify.breakpoint.mdAndUp"
    app
    clipped-left
    color="blue-grey darken-4"
    dark
  >
    <Logo text />
    <v-spacer />
    <v-toolbar-items v-if="$vuetify.breakpoint.lgAndUp">
      <v-btn :href="helpLink" target="_blank" text>
        {{ $tc('global.navigation.help') }}
        <span class="blue-grey--text"><v-icon small right>mdi-open-in-new</v-icon></span>
      </v-btn>
    </v-toolbar-items>
    <UserMeta />
  </v-app-bar>
</template>

<script>
import UserMeta from '@/components/navigation/UserMeta.vue'
import Logo from '@/components/navigation/Logo.vue'
import { getEnv } from '@/environment.js'

export default {
  name: 'NavigationDefault',
  components: {
    UserMeta,
    Logo,
  },
  computed: {
    helpLink() {
      return getEnv().HELP_LINK
    },
  },
}
</script>
