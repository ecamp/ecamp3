<template>
  <auth-container>
    <h1 class="display-1">{{ $tc('views.auth.activate.title') }}</h1>

    <div v-if="loading" class="text-center">
      <v-progress-circular :size="100" indeterminate color="green" />
    </div>

    <div v-if="activated === true">
      <v-alert type="success" class="my-4 text--green text--darken-2">
        {{ $tc('views.auth.activate.success') }}
      </v-alert>
      <v-spacer />
      <v-btn
        v-if="!loading"
        color="primary"
        :to="{ name: 'login' }"
        x-large
        class="my-4"
        block
      >
        {{ $tc('views.auth.registerDone.login') }}
      </v-btn>
    </div>

    <div v-if="activated === false">
      <v-alert type="error" class="my-4 text--red text--darken-2">
        {{ $tc('views.auth.activate.error') }}
      </v-alert>
    </div>
  </auth-container>
</template>

<script>
import AuthContainer from '@/components/layout/AuthContainer.vue'

export default {
  name: 'Activate',
  components: { AuthContainer },
  props: {
    userId: { type: String, required: true },
    activationKey: { type: String, required: true },
  },
  data() {
    return {
      loading: true,
      activated: null,
    }
  },
  mounted() {
    // TODO: Change, when templated links are available
    // this.api.href(this.api.get(), 'users', { userId: this.userId }).then(url => {
    //  this.api.patch(url, { activationKey: this.activationKey })
    // })

    this.api.href(this.api.get(), 'users').then((url) => {
      this.api
        .patch(url + '/' + this.userId + '/activate', {
          activationKey: this.activationKey,
        })
        .then(
          () => {
            this.loading = false
            this.activated = true
          },
          () => {
            this.loading = false
            this.activated = false
          }
        )
    })
  },
}
</script>

<style></style>
