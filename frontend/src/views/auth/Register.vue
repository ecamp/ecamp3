<template>
  <auth-container>
    <h1 class="text-h4 text-center">{{ $t('views.auth.register.title') }}</h1>

    <VeeForm v-slot="{ handleSubmit }">
      <e-form name="profile">
        <v-form @submit.prevent="handleSubmit(register)">
          <e-text-field
            v-model="firstname"
            append-inner-icon="mdi-account-outline"
            autocomplete="given-name"
            :density="$vuetify.display.xs ? 'comfortable' : undefined"
            path="firstname"
            type="text"
            vee-rules="required"
          />

          <e-text-field
            v-model="surname"
            append-inner-icon="mdi-account-outline"
            autocomplete="family-name"
            :density="$vuetify.display.xs ? 'comfortable' : undefined"
            path="surname"
            type="text"
            vee-rules="required"
          />

          <e-text-field
            v-model="nickname"
            append-inner-icon="mdi-account-outline"
            autocomplete="nickname"
            :density="$vuetify.display.xs ? 'comfortable' : undefined"
            path="nickname"
            type="text"
          />

          <e-text-field
            v-model="email"
            append-inner-icon="mdi-at"
            autocomplete="username"
            :density="$vuetify.display.xs ? 'comfortable' : undefined"
            path="email"
            type="email"
            vee-rules="email|required"
          />

          <e-password-field
            v-model="pw1"
            autocomplete="new-password"
            :density="$vuetify.display.xs ? 'comfortable' : undefined"
            loading
            maxlength="128"
            minlength="12"
            passwordrules="minlength: 12; maxlength: 128;"
            path="password"
            validate-on-blur
            vee-rules="required|min:12|max:128"
            @input="(event) => debouncedPasswordStrengthCheck(event.target.value)"
          >
            <template #loader>
              <v-progress-linear
                :color="passwordStrengthColor"
                :model-value="passwordStrength"
                height="5"
              />
            </template>
          </e-password-field>

          <e-password-field
            v-model="pw2"
            :label="$t('views.auth.register.passwordConfirmation')"
            autocomplete="new-password"
            :density="$vuetify.display.xs ? 'comfortable' : undefined"
            maxlength="128"
            minlength="12"
            passwordrules="minlength: 12; maxlength: 128;"
            path="passwordConfirmation"
            validate-on-blur
            vee-rules="required|confirmed:@password"
          />

          <e-select
            v-model="language"
            :items="availableLocales"
            :density="$vuetify.display.xs ? 'comfortable' : undefined"
            path="language"
          />

          <e-checkbox
            v-if="termsOfServiceLink"
            v-model="tos"
            path="tos"
            :label="$t('views.auth.register.acceptTermsOfService')"
            :vee-rules="{ required: { allowFalse: false } }"
            class="align-center"
          >
            <template #label>
              <span :class="{ 'body-2': $vuetify.display.xs }" style="hyphens: auto">
                {{ $t('views.auth.register.acceptTermsOfService') }}
              </span>
            </template>
            <template #append>
              <v-btn
                :href="termsOfServiceLink"
                :title="$t('global.button.open')"
                class="px-1"
                :density="$vuetify.display.xs ? 'comfortable' : undefined"
                min-width="0"
                tabindex="-1"
                target="_blank"
                variant="text"
              >
                <v-icon size="small">mdi-open-in-new</v-icon>
              </v-btn>
            </template>
          </e-checkbox>

          <p class="mt-0 mb-4 text--secondary text-left">
            <small>
              <span style="color: #d32f2f">*</span>
              {{ $t('views.auth.register.requiredField') }}
            </small>
          </p>

          <v-btn block color="primary" type="submit" size="x-large">
            <v-progress-circular v-if="registering" indeterminate size="24" />
            <v-spacer />
            <span>{{ $t('views.auth.register.register') }}</span>
            <v-spacer />
            <icon-spacer />
          </v-btn>
        </v-form>
      </e-form>
    </VeeForm>

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
import { componentI18n } from '@/plugins/i18n'
import { passwordStrengthMixin } from '../../mixins/passwordStrengthMixin.js'
import { parseTemplate } from 'url-template'
import { getEnv } from '@/environment.js'
import EForm from '@/components/form/base/EForm.vue'
import { Form as VeeForm } from 'vee-validate'
import { useToast } from 'vue-toastification'

export default {
  name: 'Register',
  components: {
    EForm,
    AuthContainer,
    VeeForm,
  },
  mixins: [passwordStrengthMixin],
  setup() {
    const toast = useToast()
    return { toast }
  },
  data() {
    return {
      registering: false,
      firstname: '',
      surname: '',
      nickname: '',
      email: '',
      pw1: '',
      pw2: '',
      language: '',
      tos: false,
      recaptcha: null,
    }
  },
  head() {
    return {
      title: this.$t('views.auth.register.register'),
    }
  },
  computed: {
    formData() {
      return {
        firstname: this.firstname,
        surname: this.surname,
        nickname: this.nickname,
        email: this.email,
        password: this.pw1,
        language: this.language,
      }
    },
    availableLocales() {
      return componentI18n.availableLocales.map((l) => ({
        value: l,
        text: this.$t('global.language', 1, { locale: l }),
      }))
    },
    termsOfServiceLink() {
      const currentLanguage = this.language || ''
      return (
        parseTemplate(getEnv().TERMS_OF_SERVICE_LINK_TEMPLATE || '').expand({
          lang: currentLanguage.substring(0, 2),
        }) || false
      )
    },
  },
  watch: {
    language() {
      if (componentI18n.availableLocales.includes(this.language)) {
        this.$store.commit('setLanguage', this.language)
      }
    },
  },
  mounted() {
    this.language = navigator.language.split('-')[0]

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
            nickname: this.formData.nickname,
            email: this.formData.email,
            language: this.formData.language,
          },
          recaptchaToken: recaptchaToken,
        })
        .then(() => this.$router.push({ name: 'register-done' }))
        .catch((e) => {
          this.toast.error(errorToMultiLineToast(e))
          this.registering = false
        })
    },
  },
}
</script>
