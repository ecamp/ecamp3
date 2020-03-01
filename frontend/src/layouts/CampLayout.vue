<template>
  <v-app style="background: #F4F4F4">
    <!-- left side drawer (desktop)-->
    <v-navigation-drawer v-if="$vuetify.breakpoint.smAndUp"
                         v-model="drawer" app
                         clipped permanent
                         :mini-variant.sync="mini"
                         mini-variant-width="40"
                         color="grey lighten-2">
      <v-btn v-if="mini" icon>
        <v-icon>mdi-format-list-bulleted-triangle</v-icon>
      </v-btn>
      <v-btn v-if="!mini" icon
             fixed
             class="ma-2"
             style="z-index: 10;"
             right @click.stop="mini = !mini">
        <v-icon>mdi-chevron-left</v-icon>
      </v-btn>

      <router-view v-if="!mini" name="aside" />
    </v-navigation-drawer>

    <!-- second menu/tool bar -->
    <v-app-bar v-if="$vuetify.breakpoint.smAndUp"
               app
               clipped-left hide-on-scroll
               color="blue-grey darken-4" dark>
      <v-toolbar-items>
        <v-btn icon
               large
               exact
               class="title"
               :to="{name: 'home'}">
          ⛺
        </v-btn>
      </v-toolbar-items>

      <v-overflow-btn class="my-2 ec-campselect"
                      label="Camp lädt"
                      :editable="editableCampButton" single-line
                      :items="lastCamps.items"
                      item-text="title" item-value="id"
                      :value="camp().id"
                      hide-details return-object
                      readonly
                      @change="changeCamp"
                      @blur="editableCampButton = false"
                      @click:append.capture="editableCampButton = !editableCampButton">
        <template v-slot:selection="data">
          <div class="v-select__selection v-select__selection--comma v-toolbar__title"
               @mousedown="$router.push(campRoute(data.item))">
            {{ data.item.title }}
          </div>
        </template>
      </v-overflow-btn>

      <v-toolbar-items>
        <v-btn text :to="campRoute(camp(), 'periods')">
          <v-icon :left="$vuetify.breakpoint.mdAndUp">mdi-calendar-text</v-icon>
          <span class="sr-only-sm-and-down">Events</span>
        </v-btn>
        <v-btn text :to="campRoute(camp(), 'picasso')">
          <v-icon :left="$vuetify.breakpoint.mdAndUp">mdi-calendar-month</v-icon>
          <span class="sr-only-sm-and-down">Picasso</span>
        </v-btn>
        <v-btn text :to="campRoute(camp(), 'collaborators')">
          <v-icon :left="$vuetify.breakpoint.mdAndUp">mdi-account-group</v-icon>
          <span class="sr-only-sm-and-down">Team</span>
        </v-btn>
        <v-btn text :to="campRoute(camp())" exact>
          <v-icon :left="$vuetify.breakpoint.mdAndUp">mdi-information</v-icon>
          <span class="sr-only-sm-and-down">Admin</span>
        </v-btn>
      </v-toolbar-items>
      <v-spacer />
      <v-btn v-if="loggedIn" text
             @click="logout">
        <v-icon v-if="logoutIcon" :left="$vuetify.breakpoint.mdAndUp">{{ logoutIcon }}</v-icon>
        <v-progress-circular v-else indeterminate
                             size="18"
                             class="mr-2" />
        <span class="sr-only-sm-and-down">Log out</span>
      </v-btn>
      <v-btn v-else text
             :to="{ name: 'login' }">
        <v-icon :left="$vuetify.breakpoint.mdAndUp">mdi-login</v-icon>
        <span class="sr-only-sm-and-down">Log in</span>
      </v-btn>
      <v-btn icon>
        <v-icon>mdi-account</v-icon>
      </v-btn>
    </v-app-bar>

    <!-- main content -->
    <v-content>
      <v-container
        fluid>
        <router-view />
      </v-container>
    </v-content>

    <v-bottom-navigation v-if="$vuetify.breakpoint.xs" fixed grow>
      <v-btn :to="{name: 'camp'}" exact>
        <span>{{ camp().name }}</span>
        <v-icon>mdi-tent</v-icon>
      </v-btn>
      <v-btn :to="{name: 'camp/periods'}">
        <span>Events</span>
        <v-icon>mdi-calendar-text</v-icon>
      </v-btn>
      <v-btn :to="{name: 'camp/picasso'}">
        <span>Picasso</span>
        <v-icon>mdi-calendar-month</v-icon>
      </v-btn>
      <v-btn :to="{name: 'camp/collaborators'}" exact>
        <span>Team</span>
        <v-icon>mdi-account-group</v-icon>
      </v-btn>
      <v-btn :to="{name: 'home'}">
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
import { campRoute, campFromRoute } from '@/router'

export default {
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

  .ec-campselect .v-select__slot input {
    font-size: 1.25rem;
  }

  .ec-campselect {
    flex: 0 1 300px;
  }

  .ec-campselect.v-overflow-btn .v-input__control::before, .ec-campselect.v-overflow-btn .v-input__slot::before {
    border: none;
    height: 0;
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

  .theme--dark.v-overflow-btn {
    &:hover, &.v-input--is-focused, &.v-select--is-menu-active {
      .v-input__slot {
        background: map-get($blue-grey, 'darken-3')
      }
    }
  }
</style>
