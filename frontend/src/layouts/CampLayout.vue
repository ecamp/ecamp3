<template>
  <v-app>
    <!-- left side drawer (desktop)-->
    <v-navigation-drawer v-if="$vuetify.breakpoint.smAndUp"
                         v-model="drawer" app
                         clipped permanent
                         :mini-variant.sync="mini"
                         mini-variant-width="40"
                         color="blue-grey lighten-4">
      <v-btn v-if="mini" icon>
        <v-icon>mdi-format-list-bulleted-triangle</v-icon>
      </v-btn>
      <v-spacer />
      <v-btn v-if="!mini" icon
             fixed
             class="ec-drawer-collapse ma-2"
             style="z-index: 10;"
             right @click.stop="mini = !mini">
        <v-icon>mdi-chevron-left</v-icon>
      </v-btn>

      <v-divider />

      <router-view v-if="!mini" name="aside" />
    </v-navigation-drawer>

    <!-- second menu/tool bar -->
    <v-app-bar v-if="$vuetify.breakpoint.smAndUp"
               app
               clipped-left
               color="blue-grey darken-4" dark>

      <v-toolbar-items>
        <v-btn text class="px-2"
               min-width="0" rounded
               exact :to="{ name: 'home'}">
          <v-toolbar-title>
            <i>🏕</i>️
          </v-toolbar-title>
        </v-btn>
        <v-btn text class="justify-start px-2"
               exact :to="campRoute(camp(), 'picasso')"
               width="216">
          <v-toolbar-title>
            {{ camp().title | loading('Camp wird geladen…') }}
          </v-toolbar-title>
        </v-btn>
      </v-toolbar-items>

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
      <global-search/>
      <v-menu offset-y dark
              right content-class="ec-usermenu"
              transition="slide-y-transition"
              z-index="5">
        <template v-slot:activator="{ on,value }">
          <v-toolbar-items>
            <v-btn right text
                   :class="{ 'v-btn--open': value }" v-on="on">
              <span v-if="loggedIn" class="sr-only-sm-and-down">
              {{ username }}
              </span>
              <v-icon class="ma-2">mdi-account</v-icon>
            </v-btn>
          </v-toolbar-items>
        </template>
        <v-list dense class="user-nav"
                light color="blue-grey lighten-5">
          <v-list-item block
                       exact :to="{ name: 'camps', params: { groupName: encodeURI('Pfadi Bewegung Schweiz') } }">
            <v-icon left>mdi-format-list-bulleted-triangle</v-icon>
            <span>Meine Camps</span>
          </v-list-item>
          <v-list-item v-if="loggedIn" block @click="logout">
            <v-icon v-if="logoutIcon" left>{{ logoutIcon }}</v-icon>
            <v-progress-circular v-else indeterminate
                                 size="18" class="mr-2" />
            <span>Log out</span>
          </v-list-item>
          <v-list-item v-else :to="{ name: 'login' }" block>
            <v-icon left>mdi-login</v-icon>
            <span>Log in</span>
          </v-list-item>
        </v-list>
      </v-menu>
    </v-app-bar>

    <!-- main content -->
    <v-content>
      <v-container
        fluid>
        <router-view />
      </v-container>
    </v-content>

    <v-bottom-navigation v-if="$vuetify.breakpoint.xs" fixed grow>
      <v-btn>
        <span>Material</span>
        <v-icon>mdi-package-variant</v-icon>
      </v-btn>
      <v-btn>
        <span>Tasks</span>
        <v-icon>mdi-format-list-checks</v-icon>
      </v-btn>
      <v-btn :to="{name: 'camp/picasso'}">
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

    <!-- footer -->
    <v-footer v-if="$vuetify.breakpoint.mdAndUp"
              app color="grey lighten-5">
      eCamp v0.0.1
    </v-footer>
  </v-app>
</template>

<script>
import { campRoute, campFromRoute } from '@/router'
import GlobalSearch from '../components/Search'

export default {
  components: { GlobalSearch },
  data () {
    return {
      editableCampButton: false,
      drawer: false,
      mini: !this.$vuetify.breakpoint.mdAndUp,
      logoutIcon: 'mdi-logout'
    }
  },
  computed: {
    loggedIn () {
      return this.$auth.isLoggedIn()
    },
    lastCamps () {
      return this.api.get().camps()
    },
    camp () {
      return campFromRoute(this.$route)
    },
    username () {
      return this.$auth.username()
    }
  },
  created () {
    this.$vuetify.theme.themes.dark.grey = 'ffcc00'
  },
  methods: {
    logout () {
      this.logoutIcon = ''
      this.$auth.logout().then(() => this.$router.replace({ name: 'login' }))
    },
    changeCamp (selectedCamp) {
      this.$router.push(campRoute(selectedCamp))
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
  .v-navigation-drawer--temporary.v-navigation-drawer--clipped {
    z-index: 5;
    margin-top: 116px;
  }

  .v-btn.ec-drawer-collapse {
    right: 0;
  }

  .v-content {
    height: 100vh;
    position: relative;
  }

  .v-content__wrap {
    overflow: scroll;
    position: static;
  }

  @media #{map-get($display-breakpoints, 'sm-and-down')}{
    .container.container--fluid {
      padding: 0;

      & > .v-card {
        border-radius: 0;
      }
    }
    .sr-only-sm-and-down {
      position: absolute;
      width: 1px;
      height: 1px;
      padding: 0;
      overflow: hidden;
      clip: rect(0, 0, 0, 0);
      white-space: nowrap;
      clip-path: inset(50%);
      border: 0;
    }
  }
</style>
