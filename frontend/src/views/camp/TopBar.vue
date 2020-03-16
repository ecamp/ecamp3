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
    <v-menu offset-y dark
            right content-class="ec-usermenu"
            transition="slide-y-transition"
            z-index="5">
      <template v-slot:activator="{ on,value }">
        <v-toolbar-items>
          <v-btn right text
                 :class="{ 'v-btn--open': value }" v-on="on">
            <span class="sr-only-sm-and-down">
              {{ username }}
            </span>
            <v-icon class="ma-2">mdi-account</v-icon>
          </v-btn>
        </v-toolbar-items>
      </template>
      <v-list dense class="user-nav"
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
        <v-list-item block @click="logout">
          <v-icon v-if="logoutIcon" left>{{ logoutIcon }}</v-icon>
          <v-progress-circular v-else indeterminate
                               size="18" class="mr-2" />
          <span>Log out</span>
        </v-list-item>
      </v-list>
    </v-menu>
  </v-app-bar>
</template>

<script>
import { campFromRoute, campRoute } from '@/router'
import DesktopSearch from '@/components/base/DesktopSearch'

export default {
  name: 'TopBar',
  components: {
    DesktopSearch,
    Logo: () => import('@/components/base/Logo')
  },
  data () {
    return {
      logoutIcon: 'mdi-logout'
    }
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

<style lang="scss">

</style>
