<template>
  <auth-card>
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
  </auth-card>
</template>

<script>
import AuthCard from '@/components/layout/AuthCard'

export default {
  name: 'RegisterDone',
  components: { AuthCard }
}
</script>

<style>
</style>
