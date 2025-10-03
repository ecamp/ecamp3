<template>
  <auth-container>
    <h1 class="text-h4">{{ $t('views.auth.registerDone.title') }}</h1>
    <v-alert prominent type="success" class="my-4 text--green text--darken-2">
      <div class="text-h6">
        {{ $t('views.auth.registerDone.success') }}
      </div>
      <div>
        {{ $t('views.auth.registerDone.message') }}
      </div>
    </v-alert>
    <v-spacer />
    <v-btn :to="{ name: 'login' }" block class="my-4" color="primary" size="x-large">
      {{ $t('global.button.login') }}
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

<style scoped></style>
