<template>
  <v-app-bar
    v-if="$vuetify.breakpoint.smAndUp"
    app clipped-left
    color="blue-grey darken-4" dark>
    <logo text />
    <v-spacer />
    <user-meta />
  </v-app-bar>
  <v-bottom-navigation
    v-else grow
    app
    background-color="blue-grey darken-4" dark>
    <v-btn :to="{name: 'home'}" exact>
      <span>{{ $tc('views.navigationDefault.home') }}</span>
      <v-icon>mdi-home</v-icon>
    </v-btn>
    <v-btn :to="{name: 'camps'}">
      <span>{{ $tc('views.navigationDefault.myCamps', 2) }}</span>
      <v-icon>mdi-format-list-bulleted-triangle</v-icon>
    </v-btn>
    <v-btn :to="{name: 'profile'}">
      <span>{{ $tc('views.navigationDefault.profile') }}</span>
      <v-icon>mdi-account</v-icon>
    </v-btn>
  </v-bottom-navigation>
</template>

<script>
import UserMeta from '@/components/navigation/UserMeta'
import Logo from '@/components/navigation/Logo'

export default {
  name: 'NavigationDefault',
  components: {
    UserMeta,
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
    }
  }
}
</script>

<style scoped>

</style>
