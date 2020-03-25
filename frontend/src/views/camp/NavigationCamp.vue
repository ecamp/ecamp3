<template>
  <div>
    <v-app-bar v-if="$vuetify.breakpoint.smAndUp" app
               clipped-left
               color="blue-grey darken-4" dark>
      <logo>
        <v-btn :to="campRoute(camp(), 'program')" class="justify-start px-2"
               text
               width="216">
          <v-toolbar-title>
            {{ camp().title | loading('Camp wird geladen…') }}
          </v-toolbar-title>
        </v-btn>
      </logo>

      <v-toolbar-items>
        <v-btn :to="campRoute(camp(), 'collaborators')" text>
          <v-icon :left="$vuetify.breakpoint.mdAndUp">mdi-account-group</v-icon>
          <span class="sr-only-sm-and-down">Team</span>
        </v-btn>
        <v-btn :to="campRoute(camp(), 'admin')" text>
          <v-icon :left="$vuetify.breakpoint.mdAndUp">mdi-cogs</v-icon>
          <span class="sr-only-sm-and-down">Admin</span>
        </v-btn>
      </v-toolbar-items>
      <v-spacer />
      <search-desktop />
      <user-meta />
    </v-app-bar>
    <v-bottom-navigation v-else app
                         fixed grow>
      <v-btn>
        <span>Material</span>
        <v-icon>mdi-package-variant</v-icon>
      </v-btn>
      <v-btn>
        <span>Tasks</span>
        <v-icon>mdi-format-list-checks</v-icon>
      </v-btn>
      <v-btn :to="{name: 'camp/program'}">
        <span>Camp</span>
        <v-icon large>mdi-tent</v-icon>
      </v-btn>
      <v-btn :to="{name: 'camp/collaborators'}" exact>
        <span>Team</span>
        <v-icon>mdi-account-group</v-icon>
      </v-btn>
      <v-btn :to="{name: 'camp/admin'}">
        <span>Admin</span>
        <v-icon>mdi-cogs</v-icon>
      </v-btn>
    </v-bottom-navigation>
  </div>
</template>

<script>
import { campFromRoute, campRoute } from '@/router'
import SearchDesktop from '@/components/navigation/SearchDesktop'
import UserMeta from '@/components/navigation/UserMeta'
import Logo from '@/components/navigation/Logo'

export default {
  name: 'NavigationCamp',
  components: {
    UserMeta,
    SearchDesktop,
    Logo
  },
  computed: {
    camp () {
      return campFromRoute(this.$route)
    }
  },
  methods: {
    campRoute
  }
}
</script>

<style lang="scss">

</style>
