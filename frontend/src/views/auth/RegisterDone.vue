<template>
  <auth-container>
    <h1 class="display-1">Konto erstellen…</h1>
    <v-alert type="success" class="my-4 text--green text--darken-2">
      Successfully registered.
    </v-alert>
    <v-spacer />
    <v-btn color="primary" :to="{name: 'login'}"
           x-large
           class="my-4" block>
      Login
    </v-btn>
  </auth-container>
</template>

<script>
import AuthContainer from '@/components/layout/AuthContainer'

export default {
  name: 'RegisterDone',
  components: { AuthContainer }
}
</script>

<style>
</style>
