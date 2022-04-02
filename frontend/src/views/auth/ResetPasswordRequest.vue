<template>
  <auth-container>
    <h1 class="display-1 text-center mb-4">{{ $tc('views.auth.resetPasswordRequest.title') }}</h1>

    <v-alert v-if="status == 'success'" type="success">
      {{ $tc('views.auth.resetPasswordRequest.successMessage') }}
    </v-alert>
    
    <v-alert v-if="status == 'error'" type="error">
      {{ $tc('views.auth.resetPasswordRequest.errorMessage') }}
    </v-alert>


    <v-form v-if="status == 'mounted' || status == 'sending'" @submit.prevent="resetPassword">
      <e-text-field
        v-model="email"
        :label="$tc('entity.user.fields.email')"
        name="email"
        vee-rules="email"
        append-icon="mdi-at"
        :dense="$vuetify.breakpoint.xsOnly"
        type="text"
        autofocus />

      <v-btn
        type="submit"
        block
        :color="email ? 'blue darken-2' : 'blue lightne-4'"
        :disabled="!email"
        outlined
        :x-large="$vuetify.breakpoint.smAndUp"
        class="my-4">
        <v-progress-circular v-if="status == 'sending'" indeterminate size="24" />
        <v-icon v-else>$vuetify.icons.ecamp</v-icon>
        <v-spacer />
        <span>{{ $tc('views.auth.resetPasswordRequest.send') }}</span>
        <v-spacer />
        <icon-spacer />
      </v-btn>
    </v-form>
    <p class="mt-8 mb0 text--secondary text-center">
      <router-link :to="{ name: 'login' }">
        {{ $tc('views.auth.resetPasswordRequest.back') }}
      </router-link>
    </p>
  </auth-container>
</template>

<script>
export default {
  name: 'ResetPasswordRequest',

  data () {
    return {
      email: '',
      status: 'mounted'
    }
  },

  methods: {
    resetPassword () {
      this.status = 'sending'
      this.$auth.resetPasswordRequest(this.email).then(() => {
        this.status = 'success'
      }).catch(() => {
        this.status = 'error'
      })
    }
  }
}
</script>
