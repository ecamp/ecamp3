<template>
  <auth-container>
    <h1 class="display-1 text-center mb-4">{{ $tc('views.auth.resetPassword.title') }}</h1>

    <div v-if="status == 'loading'" style="text-align: center">
      <v-progress-circular
        :size="70"
        :width="7"
        indeterminate />
    </div>

    <v-alert v-if="status == 'loading-failed'" type="error">
      {{ $tc('views.auth.resetPassword.invalidRequest') }}
    </v-alert>

    <v-alert v-if="status == 'success'" type="success">
      {{ $tc('views.auth.resetPassword.successMessage') }}
    </v-alert>

    <v-alert v-if="status == 'failed'" type="error">
      {{ $tc('views.auth.resetPassword.errorMessage') }}
    </v-alert>

    <v-form
      v-if="status == 'loaded' || status=='reseting'"
      @submit.prevent="resetPassword">
      <ValidationObserver>
        <e-text-field
          :value="email"
          :label="$tc('entity.user.fields.email')"
          name="email"
          append-icon="mdi-at"
          :dense="$vuetify.breakpoint.xsOnly"
          type="text"
          readonly />

        <e-text-field
          v-model="password"
          :label="$tc('views.auth.resetPassword.password')"
          name="Password"
          vee-id="password"
          vee-rules="min:8"
          validate-on-blur
          :dense="$vuetify.breakpoint.xsOnly"
          type="password"
          loading
          autofocus>
          <template #progress>
            <v-progress-linear
              :value="strength"
              :color="color"
              absolute
              height="5" />
          </template>
        </e-text-field>

        <e-text-field
          v-model="confirmation"
          :label="$tc('views.auth.resetPassword.passwordConfirmation')"
          name="Confirmation"
          vee-id="confirmation"
          vee-rules="pwConfirmed:@password"
          vee-rules-old="confirmed:password"
          validate-on-blur
          :dense="$vuetify.breakpoint.xsOnly"
          type="password" />

        <v-btn
          type="submit"
          block
          :color="email ? 'blue darken-2' : 'blue lightne-4'"
          :disabled="!email"
          outlined
          :x-large="$vuetify.breakpoint.smAndUp"
          class="my-4">
          <v-progress-circular v-if="status=='reseting'" indeterminate size="24" />
          <v-icon v-else>$vuetify.icons.ecamp</v-icon>
          <v-spacer />
          <span>{{ $tc('views.auth.resetPassword.send') }}</span>
          <v-spacer />
          <icon-spacer />
        </v-btn>
      </ValidationObserver>
    </v-form>
    <p class="mt-8 mb0 text--secondary text-center">
      <router-link :to="{ name: 'login' }">
        {{ $tc('views.auth.resetPassword.back') }}
      </router-link>
    </p>
  </auth-container>
</template>

<script>
import { ValidationObserver } from 'vee-validate'
import { passwordStrength } from 'check-password-strength'

export default {
  name: 'ResetPassword',
  components: { ValidationObserver },
  props: {
    id: { type: String, required: true }
  },

  data () {
    return {
      email: null,
      password: '',
      confirmation: '',
      status: 'loading'
    }
  },

  computed: {
    strengthInfo () {
      return passwordStrength(this.password)
    },
    strength () {
      if (this.strengthInfo.length === 0) return 0
      return (1 + this.strengthInfo.id) * 25
    },
    color () {
      if (this.strength <= 25) return 'red'
      if (this.strength <= 50) return 'orange'
      if (this.strength <= 75) return 'yellow'
      return 'green'
    }
  },

  async mounted () {
    const url = await this.api.href(this.api.get(), 'resetPassword', { id: this.id })
    this.api.get(url)._meta.load.then(info => {
      this.email = info.email
      this.status = 'loaded'
    }, e => {
      this.status = 'loading-failed'
    })
  },

  methods: {
    async resetPassword () {
      this.status = 'reseting'
      this.$auth.resetPassword(this.id, this.password).then(() => {
        this.status = 'success'
      }).catch((e) => {
        this.status = 'failed'
      })
    }
  }
}
</script>
