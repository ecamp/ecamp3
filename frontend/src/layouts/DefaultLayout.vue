<template>
  <v-app>
    <!-- second menu/tool bar -->
    <v-app-bar v-if="$vuetify.breakpoint.smAndUp"
               app
               clipped-left hide-on-scroll
               color="blue-grey darken-4" dark>
      <v-toolbar-items>
        <v-btn text class="px-2"
               exact :to="{ name: 'home'}">
          <v-toolbar-title>
            <i>🏕</i>️<span class="ml-4 mr-2">eCamp<sup class="blue-grey--text">v3</sup></span>
          </v-toolbar-title>
        </v-btn>
      </v-toolbar-items>
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
              <span v-if="loggedIn" class="sr-only-sm-and-down">
                {{ username }}
              </span>
              <v-icon class="ma-2">mdi-account</v-icon>
            </v-btn>
          </v-toolbar-items>
        </template>
        <v-list dense
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

    <v-bottom-navigation v-if="loggedIn && $vuetify.breakpoint.xs" fixed grow>
      <v-btn :to="{name: 'home'}" exact>
        <span>Home</span>
        <v-icon>mdi-home</v-icon>
      </v-btn>
      <v-btn :to="{name: 'camps'}" exact>
        <span>Camps</span>
        <v-icon>mdi-format-list-bulleted-triangle</v-icon>
      </v-btn>
      <v-btn :to="{name: 'profile'}">
        <span>Profile</span>
        <v-icon>mdi-account</v-icon>
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
    prevent (event) {
      event.stopImmediatePropagation()
      event.preventDefault()
      event.cancelBubble = true
      return null
    }
  }
}
</script>

<style lang="scss">
  .v-navigation-drawer--temporary.v-navigation-drawer--clipped {
    z-index: 5;
    margin-top: 116px;
  }

  .v-btn--open {
    background: #B0BEC5 !important;
    color: rgba(0, 0, 0, 0.87) !important;
  }

  .ec-usermenu {
    border-top-left-radius: 0 !important;
    border-top-right-radius: 0 !important;
    right: 0;
    left: inherit !important;
    .v-list {
      border-radius: 0;
    }
  }

  .v-app-bar .v-toolbar__content {
    padding-left: 0;
    padding-right: 0;
    width: 100%;
  }

  .v-navigation-drawer__content .v-card {
    border-top-left-radius: 0!important;
    border-top-right-radius: 0!important;
  }

  @media #{map-get($display-breakpoints, 'xs-only')}{
    .v-content .container {
      min-height: calc(100% - 56px);
      display: flex;

      .v-card {
        margin-left: 0 !important;
        margin-right: 0 !important;
        flex: auto;
      }
    }
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

  .ec-menu-left {
    left: 0 !important;
    font-feature-settings: 'tnum';
  }
</style>
