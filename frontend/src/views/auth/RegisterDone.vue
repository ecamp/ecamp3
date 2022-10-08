<template>
  <auth-container>
    <h1 class="display-1">{{ $tc('views.auth.registerDone.title') }}</h1>
    <v-alert prominent type="success" class="my-4 text--green text--darken-2">
      <div class="text-h6">
        {{ $tc('views.auth.registerDone.success') }}
      </div>
      <div>
        {{ $tc('views.auth.registerDone.message') }}
      </div>
    </v-alert>
    <v-spacer />
    <v-btn color="primary" :to="{ name: 'login' }" x-large class="my-4" block>
      {{ $tc('global.button.login') }}
    </v-btn>
  </auth-container>
</template>

<script>
import AuthContainer from '@/components/layout/AuthContainer.vue'

export default {
  name: 'RegisterDone',
  components: { AuthContainer },
}
</script>

<style></style>
