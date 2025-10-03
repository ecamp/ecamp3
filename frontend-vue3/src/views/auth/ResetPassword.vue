<template>
  <auth-container>
    <h1 class="display-1 text-center mb-4">
      {{ $t('views.auth.resetPassword.title') }}
    </h1>

    <div v-if="status == 'loading'" style="text-align: center">
      <v-progress-circular :size="70" :width="7" indeterminate />
    </div>

    <v-alert v-if="status == 'loading-failed'" type="error">
      {{ $t('views.auth.resetPassword.invalidRequest') }}
    </v-alert>

    <v-alert v-if="status == 'success'" type="success">
      {{ $t('views.auth.resetPassword.successMessage') }}
    </v-alert>

    <v-alert v-if="status == 'failed'" type="error">
      {{ $t('views.auth.resetPassword.errorMessage') }}
    </v-alert>

    <!--    <validation-observer v-slot="{ handleSubmit }">-->
    <e-form name="user">
      <v-form
        v-if="status == 'loaded' || status == 'reseting'"
        @submit.prevent="handleSubmit(resetPassword)"
      >
        <e-text-field
          :dense="$vuetify.display.xsOnly"
          :value="email"
          append-icon="mdi-at"
          autocomplete="username"
          path="email"
          readonly
          type="email"
        />

        <e-text-field
          v-model="password"
          :dense="$vuetify.display.xsOnly"
          append-icon="mdi-lock-outline"
          autocomplete="new-password"
          autofocus
          loading
          maxlength="128"
          minlength="12"
          passwordrules="minlength: 12; maxlength: 128;"
          path="password"
          type="password"
          validate-on-blur
          vee-rules="required|min:12|max:128"
          @input="debouncedPasswordStrengthCheck"
        >
          <template #progress>
            <v-progress-linear
              :color="passwordStrengthColor"
              :value="passwordStrength"
              absolute
              height="5"
            />
          </template>
        </e-text-field>

        <e-text-field
          v-model="confirmation"
          :dense="$vuetify.display.xsOnly"
          :label="$t('views.auth.resetPassword.passwordConfirmation')"
          append-icon="mdi-lock-outline"
          autocomplete="new-password"
          maxlength="128"
          minlength="12"
          passwordrules="minlength: 12; maxlength: 128;"
          path="passwordConfirmation"
          type="password"
          validate-on-blur
          vee-rules="required|confirmed:password"
        />

        <v-btn
          :color="email ? 'blue darken-2' : 'blue lightne-4'"
          :disabled="!email"
          :x-large="$vuetify.display.smAndUp"
          block
          class="my-4"
          outlined
          type="submit"
        >
          <v-progress-circular v-if="status == 'reseting'" indeterminate size="24" />
          <v-icon v-else>$vuetify.icons.ecamp</v-icon>
          <v-spacer />
          <span>{{ $t('views.auth.resetPassword.send') }}</span>
          <v-spacer />
          <IconSpacer />
        </v-btn>
      </v-form>
    </e-form>
    <!--    </validation-observer>-->
    <p class="mt-8 mb0 text--secondary text-center">
      <router-link :to="{ name: 'login' }">
        {{ $t('global.button.login') }}
      </router-link>
    </p>
  </auth-container>
</template>

<script>
import { load } from 'recaptcha-v3'
// import { ValidationObserver } from 'vee-validate'
import { passwordStrengthMixin } from '../../mixins/passwordStrengthMixin.js'
import { getEnv } from '@/environment'
import EForm from '@/components/form/base/EForm.vue'
import IconSpacer from '@/components/layout/IconSpacer.vue'

export default {
  name: 'ResetPassword',
  components: {
    IconSpacer,
    EForm,
    // ValidationObserver
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
