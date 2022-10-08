<template>
  <auth-container>
    <h1 class="display-1 text-center mb-4">
      {{ $tc('views.auth.resetPassword.title') }}
    </h1>

    <div v-if="status == 'loading'" style="text-align: center">
      <v-progress-circular :size="70" :width="7" indeterminate />
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

    <validation-observer v-slot="{ handleSubmit }">
      <v-form
        v-if="status == 'loaded' || status == 'reseting'"
        @submit.prevent="handleSubmit(resetPassword)"
      >
        <e-text-field
          :value="email"
          :label="$tc('entity.user.fields.email')"
          name="email"
          append-icon="mdi-at"
          :dense="$vuetify.breakpoint.xsOnly"
          type="text"
          readonly
        />

        <e-text-field
          v-model="password"
          :name="$tc('views.auth.resetPassword.password')"
          vee-id="password"
          vee-rules="required|min:12|max:128"
          validate-on-blur
          append-icon="mdi-lock-outline"
          :dense="$vuetify.breakpoint.xsOnly"
          type="password"
          loading
          autofocus
          @input="debouncedPasswordStrengthCheck"
        >
          <template #progress>
            <v-progress-linear
              :value="passwordStrength"
              :color="passwordStrengthColor"
              absolute
              height="5"
            />
          </template>
        </e-text-field>

        <e-text-field
          v-model="confirmation"
          :name="$tc('views.auth.resetPassword.passwordConfirmation')"
          vee-rules="required|confirmed:password"
          validate-on-blur
          :dense="$vuetify.breakpoint.xsOnly"
          append-icon="mdi-lock-outline"
          type="password"
        />

        <v-btn
          type="submit"
          block
          :color="email ? 'blue darken-2' : 'blue lightne-4'"
          :disabled="!email"
          outlined
          :x-large="$vuetify.breakpoint.smAndUp"
          class="my-4"
        >
          <v-progress-circular v-if="status == 'reseting'" indeterminate size="24" />
          <v-icon v-else>$vuetify.icons.ecamp</v-icon>
          <v-spacer />
          <span>{{ $tc('views.auth.resetPassword.send') }}</span>
          <v-spacer />
          <icon-spacer />
        </v-btn>
      </v-form>
    </validation-observer>
    <p class="mt-8 mb0 text--secondary text-center">
      <router-link :to="{ name: 'login' }">
        {{ $tc('views.auth.resetPassword.back') }}
      </router-link>
    </p>
  </auth-container>
</template>

<script>
import { load } from 'recaptcha-v3'
import { ValidationObserver } from 'vee-validate'
import { passwordStrengthMixin } from '../../mixins/passwordStrengthMixin.js'

export default {
  name: 'ResetPassword',
  components: { ValidationObserver },
  mixins: [passwordStrengthMixin],
  props: {
    id: { type: String, required: true },
  },

  data() {
    return {
      email: null,
      password: '',
      confirmation: '',
      status: 'loading',
      recaptcha: null,
    }
  },

  async mounted() {
    if (window.environment.RECAPTCHA_SITE_KEY) {
      this.recaptcha = load(window.environment.RECAPTCHA_SITE_KEY, {
        explicitRenderParameters: {
          badge: 'bottomleft',
        },
      })
    }

    const url = await this.api.href(this.api.get(), 'resetPassword', { id: this.id })
    this.api.get(url)._meta.load.then(
      (info) => {
        this.email = info.email
        this.status = 'loaded'
      },
      () => {
        this.status = 'loading-failed'
      }
    )
  },

  methods: {
    async resetPassword() {
      this.status = 'reseting'

      let recaptchaToken = null
      if (this.recaptcha) {
        const recaptcha = await this.recaptcha
        recaptchaToken = await recaptcha.execute('login')
      }

      this.$auth
        .resetPassword(this.id, this.password, recaptchaToken)
        .then(() => {
          this.status = 'success'
        })
        .catch(() => {
          this.status = 'failed'
        })
    },
  },
}
</script>
