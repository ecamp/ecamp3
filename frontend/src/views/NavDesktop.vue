<template>
  <v-app-bar
    v-if="$vuetify.breakpoint.smAndUp"
    app clipped-left
    color="blue-grey darken-4" dark>
    <logo text />
    <v-spacer />
    <desktop-search />
    <nav-desktop-user-menu />
  </v-app-bar>
</template>

<script>
import { campFromRoute, campRoute } from '@/router'
import DesktopSearch from '@/components/base/NavDesktopSearch'
import NavDesktopUserMenu from '@/components/base/NavDesktopUserMenu'

export default {
  name: 'NavDesktop',
  components: {
    NavDesktopUserMenu,
    DesktopSearch,
    Logo: () => import('@/components/base/Logo')
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
