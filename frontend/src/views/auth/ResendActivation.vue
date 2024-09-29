<template>
  <auth-container>
    <h1 class="display-1 text-center mb-4">
      {{ $tc('views.auth.resendActivation.title') }}
    </h1>

    <v-alert v-if="status === 'success'" type="success">
      {{ $tc('views.auth.resendActivation.successMessage') }}
    </v-alert>

    <v-alert v-if="status === 'error'" type="error">
      {{ $tc('views.auth.resendActivation.errorMessage') }}
    </v-alert>

    <v-form
      v-if="status === 'mounted' || status === 'sending'"
      @submit.prevent="resendActivationEmail"
    >
      <e-text-field
        v-model="email"
        :label="$tc('entity.profile.fields.email')"
        name="email"
        vee-rules="email"
        append-icon="mdi-at"
        :dense="$vuetify.breakpoint.xsOnly"
        type="email"
        autocomplete="username"
        autofocus
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
        <v-progress-circular v-if="status === 'sending'" indeterminate size="24" />
        <v-icon v-else>$vuetify.icons.ecamp</v-icon>
        <v-spacer />
        <span>{{ $tc('views.auth.resendActivation.send') }}</span>
        <v-spacer />
        <icon-spacer />
      </v-btn>
    </v-form>
    <p class="mt-8 mb0 text--secondary text-center">
      <router-link :to="{ name: 'login' }">
        {{ $tc('global.button.login') }}
      </router-link>
    </p>
  </auth-container>
</template>

<script>
import { load } from 'recaptcha-v3'
import { getEnv } from '@/environment.js'
import AuthContainer from '@/components/layout/AuthContainer.vue'
import IconSpacer from '@/components/layout/IconSpacer.vue'

export default {
  name: 'ResetPasswordRequest',
  components: { IconSpacer, AuthContainer },

  data() {
    return {
      email: '',
      status: 'mounted',
      recaptcha: null,
    }
  },

  mounted() {
    if (getEnv().RECAPTCHA_SITE_KEY) {
      this.recaptcha = load(getEnv().RECAPTCHA_SITE_KEY, {
        explicitRenderParameters: {
          badge: 'bottomleft',
        },
      })
    }
  },

  methods: {
    async resendActivationEmail() {
      this.status = 'sending'

      let recaptchaToken = null
      if (this.recaptcha) {
        const recaptcha = await this.recaptcha
        recaptchaToken = await recaptcha.execute('login')
      }

      this.$auth
        .resendUserActivation(this.email, recaptchaToken)
        .then(() => {
          this.status = 'success'
        })
        .catch(() => {
          this.status = 'error'
        })
    },
  },
}
</script>
