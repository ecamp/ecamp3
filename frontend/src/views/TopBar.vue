<template>
  <v-app-bar
    v-if="$vuetify.breakpoint.smAndUp"
    app clipped-left
    color="blue-grey darken-4" dark>
    <logo text/>
    <v-spacer />
    <global-search />
    <v-menu offset-y dark
            right content-class="ec-usermenu"
            transition="slide-y-transition"
            z-index="5">
      <template v-slot:activator="{ on,value }">
        <v-toolbar-items>
          <v-btn right text
                 :class="{ 'v-btn--open': value }" v-on="on">
              <span v-if="$auth.isLoggedIn()" class="sr-only-sm-and-down">
                {{ username }}
              </span>
            <v-icon class="ma-2">mdi-account</v-icon>
          </v-btn>
        </v-toolbar-items>
      </template>
      <v-list dense
              light color="blue-grey lighten-5">
        <v-list-item block :to="{ name: 'profile' }">
          <v-icon left>mdi-account</v-icon>
          <span>Profil</span>
        </v-list-item>
        <v-list-item block
                     exact :to="{ name: 'camps', params: { groupName: encodeURI('Pfadi Bewegung Schweiz') } }">
          <v-icon left>mdi-format-list-bulleted-triangle</v-icon>
          <span>Meine Camps</span>
        </v-list-item>
        <v-list-item :to="{ name: 'login' }" block>
          <v-icon left>mdi-login</v-icon>
          <span>Log in</span>
        </v-list-item>
      </v-list>
    </v-menu>
  </v-app-bar>
</template>

<script>
import { campFromRoute, campRoute } from '@/router'
import GlobalSearch from '@/components/Search'

export default {
  name: 'TopBar',
  components: {
    GlobalSearch,
    Logo: () => import('@/components/global/Logo')
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
      return this.$auth.username()
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
