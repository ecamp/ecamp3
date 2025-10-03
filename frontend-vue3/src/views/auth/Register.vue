<template>
  <auth-container>
    <h1 class="display-1 text-center">{{ $t('views.auth.register.title') }}</h1>
    <!--    <validation-observer v-slot="{ handleSubmit }">-->
    <e-form name="user">
      <v-form @submit.prevent="handleSubmit(register)">
        <e-text-field
          v-model="firstname"
          append-icon="mdi-account-outline"
          autocomplete="given-name"
          dense
          path="firstname"
          type="text"
          vee-rules="required"
        />

        <e-text-field
          v-model="surname"
          append-icon="mdi-account-outline"
          autocomplete="family-name"
          dense
          path="surname"
          type="text"
          vee-rules="required"
        />

        <e-text-field
          v-model="email"
          append-icon="mdi-at"
          autocomplete="username"
          dense
          path="email"
          type="email"
          vee-rules="email|required"
        />

        <e-text-field
          v-model="pw1"
          append-icon="mdi-lock-outline"
          autocomplete="new-password"
          dense
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
          v-model="pw2"
          :label="$t('views.auth.register.passwordConfirmation')"
          append-icon="mdi-lock-outline"
          autocomplete="new-password"
          dense
          maxlength="128"
          minlength="12"
          passwordrules="minlength: 12; maxlength: 128;"
          path="passwordConfirmation"
          type="password"
          validate-on-blur
          vee-rules="required|confirmed:password"
        />

        <e-select v-model="language" :items="availableLocales" dense path="language" />

        <e-checkbox
          v-if="termsOfServiceLink"
          v-model="tos"
          :label="$t('views.auth.register.acceptTermsOfService')"
          :vee-rules="{ required: { allowFalse: false } }"
          class="align-center"
        >
          <template #label>
            <span :class="{ 'body-2': $vuetify.display.xsOnly }" style="hyphens: auto">
              {{ $t('views.auth.register.acceptTermsOfService') }}
            </span>
          </template>
          <template #append>
            <v-btn
              :href="termsOfServiceLink"
              :title="$t('global.button.open')"
              class="px-1"
              dense
              min-width="0"
              tabindex="-1"
              target="_blank"
              text
            >
              <v-icon small>mdi-open-in-new</v-icon>
            </v-btn>
          </template>
        </e-checkbox>

        <p class="mt-0 mb-4 text--secondary text-left">
          <small>
            <span style="color: #d32f2f">*</span>
            {{ $t('views.auth.register.requiredField') }}
          </small>
        </p>

        <v-btn block color="primary" type="submit" x-large>
          <v-progress-circular v-if="registering" indeterminate size="24" />
          <v-spacer />
          <span>{{ $t('views.auth.register.register') }}</span>
          <v-spacer />
          <icon-spacer />
        </v-btn>
      </v-form>
    </e-form>
    <!--    </validation-observer>-->

    <p class="mt-8 mb-0 text--secondary text-center">
      {{ $t('views.auth.register.alreadyHaveAnAccount') }}<br />
      <router-link :to="{ name: 'login' }">
        {{ $t('global.button.login') }}
      </router-link>
    </p>
  </auth-container>
</template>

<script>
import { load } from 'recaptcha-v3'
import AuthContainer from '@/components/layout/AuthContainer.vue'
import { errorToMultiLineToast } from '@/components/toast/toasts'
import VueI18n from '@/plugins/i18n'
// import { ValidationObserver } from 'vee-validate'
import { passwordStrengthMixin } from '../../mixins/passwordStrengthMixin.js'
import { parseTemplate } from 'url-template'
import { getEnv } from '@/environment.js'
import EForm from '@/components/form/base/EForm.vue'

export default {
  name: 'Register',
  components: {
    EForm,
    AuthContainer,
    // ValidationObserver,
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
      return VueI18n.global.availableLocales.map((l) => ({
        value: l,
        text: this.$t('global.language', 1, l),
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
      if (VueI18n.global.availableLocales.includes(this.language)) {
        // this.$store.commit('setLanguage', this.language)
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
