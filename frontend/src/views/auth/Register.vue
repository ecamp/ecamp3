<template>
  <auth-container>
    <h1 class="display-1 text-center">{{ $tc('views.auth.register.title') }}</h1>
    <validation-observer v-slot="{ handleSubmit }">
      <e-form name="profile">
        <v-form @submit.prevent="handleSubmit(register)">
          <e-text-field
            v-model="firstname"
            path="firstname"
            vee-rules="required"
            append-icon="mdi-account-outline"
            dense
            type="text"
            autocomplete="given-name"
          />

          <e-text-field
            v-model="surname"
            path="surname"
            vee-rules="required"
            append-icon="mdi-account-outline"
            dense
            type="text"
            autocomplete="family-name"
          />

          <e-text-field
            v-model="email"
            path="email"
            vee-rules="email|required"
            append-icon="mdi-at"
            dense
            type="email"
            autocomplete="username"
          />

          <e-text-field
            v-model="pw1"
            path="password"
            vee-rules="required|min:12|max:128"
            validate-on-blur
            append-icon="mdi-lock-outline"
            dense
            type="password"
            autocomplete="new-password"
            minlength="12"
            maxlength="128"
            passwordrules="minlength: 12; maxlength: 128;"
            loading
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
            v-model="pw2"
            path="passwordConfirmation"
            :label="$tc('views.auth.register.passwordConfirmation')"
            vee-rules="required|confirmed:password"
            validate-on-blur
            dense
            append-icon="mdi-lock-outline"
            type="password"
            autocomplete="new-password"
            minlength="12"
            maxlength="128"
            passwordrules="minlength: 12; maxlength: 128;"
          />

          <e-select v-model="language" path="language" dense :items="availableLocales" />

          <e-checkbox
            v-if="termsOfServiceLink"
            v-model="tos"
            :vee-rules="{ required: { allowFalse: false } }"
            class="align-center"
            :label="$tc('views.auth.register.acceptTermsOfService')"
          >
            <template #label>
              <span
                style="hyphens: auto"
                :class="{ 'body-2': $vuetify.breakpoint.xsOnly }"
              >
                {{ $tc('views.auth.register.acceptTermsOfService') }}
              </span>
            </template>
            <template #append>
              <v-btn
                text
                dense
                min-width="0"
                :title="$tc('global.button.open')"
                target="_blank"
                class="px-1"
                :href="termsOfServiceLink"
                tabindex="-1"
              >
                <v-icon small>mdi-open-in-new</v-icon>
              </v-btn>
            </template>
          </e-checkbox>

          <p class="mt-0 mb-4 text--secondary text-left">
            <small>
              <span style="color: #d32f2f">*</span>
              {{ $tc('views.auth.register.requiredField') }}
            </small>
          </p>

          <v-btn type="submit" color="primary" block x-large>
            <v-progress-circular v-if="registering" indeterminate size="24" />
            <v-spacer />
            <span>{{ $tc('views.auth.register.register') }}</span>
            <v-spacer />
            <icon-spacer />
          </v-btn>
        </v-form>
      </e-form>
    </validation-observer>

    <p class="mt-8 mb-0 text--secondary text-center">
      {{ $tc('views.auth.register.alreadyHaveAnAccount') }}<br />
      <router-link :to="{ name: 'login' }">
        {{ $tc('global.button.login') }}
      </router-link>
    </p>
  </auth-container>
</template>

<script>
import { load } from 'recaptcha-v3'
import AuthContainer from '@/components/layout/AuthContainer.vue'
import { errorToMultiLineToast } from '@/components/toast/toasts'
import VueI18n from '@/plugins/i18n'
import { ValidationObserver } from 'vee-validate'
import { passwordStrengthMixin } from '../../mixins/passwordStrengthMixin.js'
import { parseTemplate } from 'url-template'
import { getEnv } from '@/environment.js'
import EForm from '@/components/form/base/EForm.vue'

export default {
  name: 'Register',
  components: {
    EForm,
    AuthContainer,
    ValidationObserver,
  },
  mixins: [passwordStrengthMixin],
  data() {
    return {
      registering: false,
      firstname: '',
      surname: '',
      email: '',
      pw1: '',
      pw2: '',
      language: '',
      tos: false,
      recaptcha: null,
    }
  },
  computed: {
    formData() {
      return {
        firstname: this.firstname,
        surname: this.surname,
        email: this.email,
        password: this.pw1,
        language: this.language,
      }
    },
    availableLocales() {
      return VueI18n.availableLocales.map((l) => ({
        value: l,
        text: this.$tc('global.language', 1, l),
      }))
    },
    termsOfServiceLink() {
      return (
        parseTemplate(getEnv().TERMS_OF_SERVICE_LINK_TEMPLATE || '').expand({
          lang: this.language.substring(0, 2),
        }) || false
      )
    },
  },
  watch: {
    language() {
      if (VueI18n.availableLocales.includes(this.language)) {
        this.$store.commit('setLanguage', this.language)
      }
    },
  },
  mounted() {
    this.language = this.$i18n.browserPreferredLocale

    if (getEnv().RECAPTCHA_SITE_KEY) {
      this.recaptcha = load(getEnv().RECAPTCHA_SITE_KEY, {
        explicitRenderParameters: {
          badge: 'bottomleft',
        },
      })
    }
  },
  methods: {
    async register() {
      this.registering = true
      let recaptchaToken = null
      if (this.recaptcha) {
        const recaptcha = await this.recaptcha
        recaptchaToken = await recaptcha.execute('login')
      }

      this.$auth
        .register({
          password: this.formData.password,
          profile: {
            firstname: this.formData.firstname,
            surname: this.formData.surname,
            email: this.formData.email,
            language: this.formData.language,
          },
          recaptchaToken: recaptchaToken,
        })
        .then(() => this.$router.push({ name: 'register-done' }))
        .catch((e) => {
          this.$toast.error(errorToMultiLineToast(e))
          this.registering = false
        })
    },
  },
}
</script>
