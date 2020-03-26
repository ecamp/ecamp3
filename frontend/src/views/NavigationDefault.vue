<template>
  <v-app-bar
    v-if="$vuetify.breakpoint.smAndUp"
    app clipped-left
    color="blue-grey darken-4" dark>
    <logo text />
    <v-spacer />
    <search-desktop />
    <user-meta />
  </v-app-bar>
  <v-bottom-navigation v-else app
                       fixed grow>
    <v-btn :to="{name: 'camps'}">
      <span>Meine Camps</span>
      <v-icon>mdi-format-list-bulleted-triangle</v-icon>
    </v-btn>
  </v-bottom-navigation>
</template>

<script>
import { campFromRoute, campRoute } from '@/router'
import SearchDesktop from '@/components/navigation/SearchDesktop'
import UserMeta from '@/components/navigation/UserMeta'
import Logo from '@/components/navigation/Logo'

export default {
  name: 'NavigationDefault',
  components: {
    UserMeta,
    SearchDesktop,
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
