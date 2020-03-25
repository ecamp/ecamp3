<template>
  <div>
    <v-app-bar
      app
      clipped-left color="blue-grey darken-4"
      dark v-if="$vuetify.breakpoint.smAndUp">
      <logo text />
      <v-spacer />
      <desktop-search />
      <nav-desktop-user-menu />
    </v-app-bar>
    <v-bottom-navigation app fixed
                         grow v-else>
      <v-btn :to="{name: 'camps'}">
        <span>Meine Camps</span>
        <v-icon>mdi-format-list-bulleted-triangle</v-icon>
      </v-btn>
    </v-bottom-navigation>
  </div>
</template>

<script>
import { campFromRoute, campRoute } from '@/router'
import DesktopSearch from '@/components/base/NavDesktopSearch'
import NavDesktopUserMenu from '@/components/base/NavDesktopUserMenu'
import Logo from '@/components/base/Logo'

export default {
  name: 'NavigationDefault',
  components: {
    NavDesktopUserMenu,
    DesktopSearch,
    Logo
  },
  data () {
    return {
      logoutIcon: 'mdi-logout'
    }
  },
  computed: {
    isLoggedIn () {
      return this.$auth.isLoggedIn()
    },
    camp () {
      return campFromRoute(this.$route)
    },
    username () {
      return this.api.get().profile().username
    }
  },
  methods: {
    logout () {
      this.logoutIcon = ''
      this.$auth.logout().then(() => this.$router.replace({ name: 'login' }))
    },
    prevent (event) {
      event.stopImmediatePropagation()
      event.preventDefault()
      event.cancelBubble = true
      return null
    },
    campRoute
  }
}
</script>

<style scoped>

</style>
