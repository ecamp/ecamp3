<template>
  <v-app
    style="background: #90A4AE">
    <!-- left side drawer -->
    <v-navigation-drawer
      app
      clipped
      :mini-variant.sync="mini"
      mini-variant-width="40"
      permanent
      color="grey lighten-2">
      <v-list-item>
        <v-btn
          v-if="mini"
          icon>
          <v-icon>mdi-chevron-right</v-icon>
        </v-btn>

        <v-list-item-title>context/sidebar</v-list-item-title>

        <v-btn
          icon
          @click.stop="mini = !mini">
          <v-icon>mdi-chevron-left</v-icon>
        </v-btn>
      </v-list-item>

      <v-divider />

      <router-view
        v-if="!mini"
        name="aside" />
    </v-navigation-drawer>

    <!-- main application menu/tool bar -->
    <v-system-bar
      app
      color="blue-grey darken-4"
      dark
      height="60">
      <v-toolbar-title class="pl-4 pr-4">
        <i>🏕</i>️
        eCamp
      </v-toolbar-title>
      <v-toolbar-items>
        <v-btn
          text
          exact
          :to="{ name: 'home'}">
          Home
        </v-btn>
        <v-btn
          text
          :to="{ name: 'camps', params: { groupName: encodeURI('Pfadi Bewegung Schweiz') } }">
          Camps
        </v-btn>
      </v-toolbar-items>

      <v-spacer />

      <v-btn
        v-if="! loggedIn"
        text
        :to="{ name: 'login' }">
        Log in
      </v-btn>
      <v-btn
        v-if="loggedIn"
        text
        :to="{ name: 'logout' }">
        Log out
      </v-btn>
      <v-btn icon>
        <v-icon>
          mdi-account
        </v-icon>
      </v-btn>
    </v-system-bar>

    <!-- second menu/tool bar -->
    <v-app-bar
      app
      clipped-left
      color="white">
      <v-toolbar-title class="pl-4 pr-4">
        SoLa 2019
      </v-toolbar-title>

      <v-toolbar-items>
        <v-btn text>
          Test
        </v-btn>
      </v-toolbar-items>
    </v-app-bar>

    <!-- main content -->
    <v-content>
      <v-container
        fluid>
        <router-view />
      </v-container>
    </v-content>

    <!-- footer -->
    <v-footer
      color="grey lighten-5"
      app>
      v0.0.1
    </v-footer>
  </v-app>
</template>

<script>

export default {
  data () {
    return {
      loggedIn: null,
      mini: true
    }
  },
  created () {
    this.$auth.subscribe(this.checkLoginStatus)
    this.checkLoginStatus()
  },
  methods: {
    async checkLoginStatus () {
      this.loggedIn = await this.$auth.isLoggedIn()
    }
  }
}
</script>

<style lang="scss">

</style>
