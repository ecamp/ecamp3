<template>
  <auth-container>
    <h1 class="text-h4 text-center mb-4">
      {{ $t('views.auth.resetPassword.title') }}
    </h1>

    <div v-if="status === 'loading'" style="text-align: center">
      <v-progress-circular :size="70" :width="7" indeterminate />
    </div>

    <v-alert v-if="status === 'loading-failed'" type="error">
      {{ $t('views.auth.resetPassword.invalidRequest') }}
    </v-alert>

    <v-alert v-if="status === 'success'" type="success">
      {{ $t('views.auth.resetPassword.successMessage') }}
    </v-alert>

    <v-alert v-if="status === 'failed'" type="error">
      {{ $t('views.auth.resetPassword.errorMessage') }}
    </v-alert>

    <Form v-if="status === 'loaded' || status === 'reseting'" @submit="resetPassword">
      <e-text-field
        :density="$vuetify.display.xs ? 'compact' : 'default'"
        :model-value="email"
        append-inner-icon="mdi-at"
        autocomplete="username"
        path="email"
        readonly
        type="email"
      />

      <e-password-field
        v-model="password"
        :density="$vuetify.display.xs ? 'compact' : 'default'"
        :label="$t('views.auth.resetPassword.password')"
        autocomplete="new-password"
        autofocus
        maxlength="128"
        minlength="12"
        passwordrules="minlength: 12; maxlength: 128;"
        path="password"
        validate-on-blur
        vee-rules="required|min:12|max:128"
        @input="(event) => debouncedPasswordStrengthCheck(event.target.value)"
      >
        <template #progress>
          <v-progress-linear
            :color="passwordStrengthColor"
            :model-value="passwordStrength"
            absolute
            height="5"
          />
        </template>
      </e-password-field>

      <e-password-field
        v-model="confirmation"
        :density="$vuetify.display.xs ? 'compact' : 'default'"
        :label="$t('views.auth.resetPassword.passwordConfirmation')"
        autocomplete="new-password"
        maxlength="128"
        minlength="12"
        passwordrules="minlength: 12; maxlength: 128;"
        path="passwordConfirmation"
        validate-on-blur
        vee-rules="required|confirmed:@password"
      />

      <v-btn
        type="submit"
        :color="email ? 'blue-darken-2' : 'blue-lighten-4'"
        block
        :disabled="!email"
        :size="$vuetify.display.smAndUp && 'large'"
        height="50"
        variant="outlined"
        class="my-4 pa-2 ec-login-button"
      >
        <v-progress-circular v-if="status === 'reseting'" indeterminate size="24" />
        <v-icon v-else size="large">$ecamp</v-icon>
        <v-spacer />
        <span>{{ $t('views.auth.resetPassword.send') }}</span>
        <v-spacer />
        <IconSpacer />
      </v-btn>
    </Form>
    <p class="mt-8 mb0 text--secondary text-center">
      <router-link :to="{ name: 'login' }">
        {{ $t('global.button.login') }}
      </router-link>
    </p>
  </auth-container>
</template>

<script>
import { load } from 'recaptcha-v3'
import { Form } from 'vee-validate'
import { passwordStrengthMixin } from '../../mixins/passwordStrengthMixin.js'
import { getEnv } from '@/environment'
import IconSpacer from '@/components/layout/IconSpacer.vue'

export default {
  name: 'ResetPassword',
  components: {
    IconSpacer,
    Form,
  },
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

  head() {
    return {
      title: this.$t('views.auth.resetPassword.title'),
    }
  },

  async mounted() {
    if (getEnv().RECAPTCHA_SITE_KEY) {
      this.recaptcha = load(getEnv().RECAPTCHA_SITE_KEY, {
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

<style lang="scss" scoped>
/* eslint-disable-next-line vue-scoped-css/no-unused-selector */
.ec-login-button.v-btn--disabled {
  color: rgba(0, 0, 0, 0.26) !important;
  opacity: 1;
}

.ec-login-button :deep(.v-btn__overlay) {
  opacity: 0.08;
}

.ec-login-button :deep(.v-btn__content) {
  width: 100%;
}
</style>
