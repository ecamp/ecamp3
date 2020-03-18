<template>
  <v-container class="fill-height justify-center align-center" fluid>
    <v-col cols="12" sm="8" md="4">
      <v-card class="elevation-12">
        <v-toolbar dense color="blue-grey lighten-5">
          <v-toolbar-title>Registering</v-toolbar-title>
        </v-toolbar>
        <v-card-text>
          <v-alert type="success">
            Successfully registered.
          </v-alert>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn color="primary" :to="{name: 'login'}">
            Login
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-col>
  </v-container>
</template>

<script>
export default {
  name: 'RegisterDone'
}
</script>

<style>
</style>
