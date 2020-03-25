<template>
  <div>
    <v-app-bar
      v-if="$vuetify.breakpoint.smAndUp"
      app clipped-left
      color="blue-grey darken-4" dark>
      <logo text />
      <v-spacer />
      <desktop-search />
      <nav-desktop-user-menu />
    </v-app-bar>
    <v-bottom-navigation v-else app
                         fixed grow>
      <v-btn :to="{name: 'camps'}">
        <span>Meine Camps</span>
        <v-icon>mdi-format-list-bulleted-triangle</v-icon>
      </v-btn>
    </v-bottom-navigation>
  </div>
</template>

<script>
import { campFromRoute, campRoute } from '@/router'
import DesktopSearch from '@/components/navigation/NavDesktopSearch'
import NavDesktopUserMenu from '@/components/navigation/NavDesktopUserMenu'
import Logo from '@/components/navigation/Logo'

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
