<template>
  <v-container>
    <card-view title="Herzlich willkommen bei eCamp" max-width="600">
      <v-list class="pt-0">
        <v-list-item v-if="loggedIn" :to="{ name: 'camps' }">
          <v-list-item-icon>
            <v-icon>mdi-format-list-bulleted-triangle</v-icon>
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title>Meine Camps</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-list-item v-if="!loggedIn" :to="{ name: 'login' }">
          <v-list-item-icon>
            <v-icon>mdi-login</v-icon>
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title>Login</v-list-item-title>
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
    </card-view>
  </v-container>
</template>

<script>
import CardView from '../components/base/CardView'

export default {
  name: 'Home',
  components: { CardView },
  computed: {
    loggedIn () {
      return this.$auth.isLoggedIn()
    },
    runningInDocker () {
      return process.env.VUE_APP_RUNNING_IN_DOCKER
    }
  }
}
</script>

<style lang="scss" scoped>
</style>
