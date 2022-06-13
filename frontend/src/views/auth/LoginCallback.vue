<template>
  <v-sheet color="green dark-3">
    <v-container class="fill-height" fluid>
      <v-row align="center" justify="center">
        <v-card>
          <v-card-text>
            <h3>{{$tc('views.auth.loginCallback.loginInProgress')</h3>
          </v-card-text>
        </v-card>
      </v-row>
    </v-container>
  </v-sheet>
</template>
<script>
export default {
  name: 'LoginCallback',
  beforeRouteEnter (to, from, next) {
    next(decodeURI(to.query.redirect || '/'))
  }
}
</script>
<style scoped></style>
