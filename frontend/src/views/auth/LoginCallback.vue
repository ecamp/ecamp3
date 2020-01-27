<template>
  <v-content>
    <v-sheet color="green dark-3">
      <v-container class="fill-height" fluid>
        <v-row align="center" justify="center">
          <v-card>
            <v-card-text><h3>Logging you in</h3></v-card-text>
          </v-card>
        </v-row>
      </v-container>
    </v-sheet>
  </v-content>
</template>
<script>
export default {
  name: 'LoginCallback',
  beforeRouteEnter (to, from, next) {
    window.opener.loginSuccess()
    window.close()
  }
}
</script>
<style scoped>
</style>
