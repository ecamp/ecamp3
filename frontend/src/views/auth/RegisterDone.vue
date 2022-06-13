<template>
  <auth-container>
    <h1 class="display-1">{{ $tc('views.auth.registerDone.title') }}</h1>
    <v-alert type="success" class="my-4 text--green text--darken-2">
      {{ $tc('views.auth.registerDone.success') }}
    </v-alert>
    <v-spacer />
    <v-btn color="primary" :to="{ name: 'login' }"
           x-large
           class="my-4" block>
      {{ $tc('views.auth.registerDone.login') }}
    </v-btn>
  </auth-container>
</template>

<script>
import AuthContainer from '@/components/layout/AuthContainer.vue'

export default {
  name: 'RegisterDone',
  components: { AuthContainer }
}
</script>

<style></style>
