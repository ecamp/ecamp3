<template>
  <v-app-bar v-if="$vuetify.breakpoint.smAndUp" app
             clipped-left
             color="blue-grey darken-4" dark>
    <logo>
      <v-btn text class="justify-start px-2"
             exact :to="campRoute(camp(), 'overview')"
             width="216">
        <v-toolbar-title>
          {{ camp().title | loading('Camp wird geladen…') }}
        </v-toolbar-title>
      </v-btn>
    </logo>

    <v-toolbar-items>
      <v-btn text :to="campRoute(camp(), 'collaborators')">
        <v-icon :left="$vuetify.breakpoint.mdAndUp">mdi-account-group</v-icon>
        <span class="sr-only-sm-and-down">Team</span>
      </v-btn>
      <v-btn text :to="campRoute(camp(), 'admin')" exact>
        <v-icon :left="$vuetify.breakpoint.mdAndUp">mdi-cogs</v-icon>
        <span class="sr-only-sm-and-down">Admin</span>
      </v-btn>
    </v-toolbar-items>
    <v-spacer />
    <desktop-search />
    <top-bar-user-menu />
  </v-app-bar>
</template>

<script>
import { campFromRoute, campRoute } from '@/router'
import DesktopSearch from '@/components/base/DesktopSearch'
import TopBarUserMenu from '@/components/base/TopBarUserMenu'

export default {
  name: 'TopBar',
  components: {
    TopBarUserMenu,
    DesktopSearch,
    Logo: () => import('@/components/base/Logo')
  },
  computed: {
    camp () {
      return campFromRoute(this.$route)
    },
    username () {
      return this.api.get().profile().username
    }
  },
  methods: {
    campRoute
  }
}
</script>

<style lang="scss">

</style>
