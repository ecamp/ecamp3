<template>
  <v-sheet color="green dark-3">
    <v-container class="fill-height" fluid>
      <v-row align="center" justify="center">
        <v-card>
          <v-card-text><h3>Logging you in</h3></v-card-text>
        </v-card>
      </v-row>
    </v-container>
  </v-sheet>
</template>
<script>
import { refreshLoginStatus } from '@/plugins/auth'

export default {
  name: 'LoginCallback',
  beforeRouteEnter (to, from, next) {
    refreshLoginStatus()
    next(decodeURI(to.query.redirect || '/'))
  }
}
</script>
<style scoped>
</style>
