<template>
  <v-card>
    <v-toolbar dense color="blue-grey lighten-5">
      <v-toolbar-title>Home</v-toolbar-title>
    </v-toolbar>
    <v-list class="pt-0">
      <v-list-item :to="{ name: 'login' }">
        <v-list-item-icon>
          <v-icon>mdi-login</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>
            Login
          </v-list-item-title>
        </v-list-item-content>
      </v-list-item>
      <v-list-item :to="{ name: 'logout' }">
        <v-list-item-icon>
          <v-icon>mdi-logout</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>
            Logout
          </v-list-item-title>
        </v-list-item-content>
      </v-list-item>
      <v-list-item :to="{ name: 'camps' }">
        <v-list-item-icon>
          <v-icon>mdi-format-list-bulleted-triangle</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>
            Camp List
          </v-list-item-title>
        </v-list-item-content>
      </v-list-item>
      <v-list-item v-if="runningInDocker" href="http://localhost:3001/setup.php?dev-data" target="_blank">
        <v-list-item-icon>
          <v-icon>mdi-database-plus</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>
            Beispiel-Camps laden
          </v-list-item-title>
          <v-list-item-subtitle>
            Dev data
          </v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
      <v-list-item v-if="runningInDocker" href="http://localhost:3001/setup.php?prod-data" target="_blank">
        <v-list-item-icon>
          <v-icon>mdi-database-plus</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>
            Beispiel-Camps laden
          </v-list-item-title>
          <v-list-item-subtitle>
            Prod data
          </v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
    </v-list>
  </v-card>
</template>

<script>
export default {
  name: 'Home',
  computed: {
    runningInDocker () {
      return process.env.VUE_APP_RUNNING_IN_DOCKER
    }
  }
}
</script>

<style lang="scss" scoped>
  .v-application--is-ltr {
    .v-list-item__action:first-child, .v-list-item__icon:first-child {
      margin-right: 20px;
    }
  }
</style>
