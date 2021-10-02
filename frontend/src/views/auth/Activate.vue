<template>
  <auth-container>
    <h1 class="display-1">{{ $tc('views.auth.activate.title') }}</h1>

    <div v-if="loading" class="text-center">
      <v-progress-circular

        :size="100"
        indeterminate
        color="green" />
    </div>

    <v-alert v-if="activated" type="success" class="my-4 text--green text--darken-2">
      {{ $tc('views.auth.activate.success') }}
    </v-alert>
    <v-spacer />

    <v-btn v-if="!loading" color="primary"
           :to="{name: 'login'}"
           x-large
           class="my-4" block>
      {{ $tc('views.auth.registerDone.login') }}
    </v-btn>
  </auth-container>
</template>

<script>
import AuthContainer from '@/components/layout/AuthContainer.vue'

export default {
  name: 'Activate',
  components: { AuthContainer },
  props: {
    userId: { type: String, required: true },
    activationKey: { type: String, required: true }
  },
  data () {
    return {
      loading: true,
      activated: null
    }
  },
  mounted () {
    // this.api.href(this.api.get(), 'users', { userId: this.userId }).then(url => {
    //  this.api.patch(url, { activationKey: this.activationKey })
    // })

    this.api.href(this.api.get(), 'user').then(url => {
      this.api.patch(url + '/' + this.userId + '/activate.jsonhal', { activationKey: this.activationKey }).then(() => {
        this.loading = false
        this.activated = true
      }, () => {
        this.loading = false
        this.activated = false
      })
    })
  }
}
</script>

<style>
</style>
