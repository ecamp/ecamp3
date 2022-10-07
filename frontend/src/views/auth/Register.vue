<template>
  <auth-container>
    <h1 class="display-1 text-center">{{ $tc('views.auth.register.title') }}</h1>
    <v-form @submit.prevent="register">
      <e-text-field
        v-model="firstname"
        :name="$tc('entity.user.fields.firstname')"
        vee-rules="required"
        append-icon="mdi-account-outline"
        dense
        type="text"
      />

      <e-text-field
        v-model="surname"
        :name="$tc('entity.user.fields.surname')"
        vee-rules="required"
        append-icon="mdi-account-outline"
        dense
        type="text"
      />

      <e-text-field
        v-model="email"
        :name="$tc('entity.user.fields.email')"
        vee-rules="email|required"
        append-icon="mdi-at"
        dense
        type="text"
      />

      <e-text-field
        v-model="pw1"
        :name="$tc('entity.user.fields.password')"
        vee-id="password"
        vee-rules="required|min:12"
        validate-on-blur
        append-icon="mdi-lock-outline"
        dense
        type="password"
      />

      <e-text-field
        v-model="pw2"
        :name="$tc('views.auth.register.passwordConfirmation')"
        :rules="pw2Rules"
        vee-rules="required"
        validate-on-blur
        dense
        append-icon="mdi-lock-outline"
        type="password"
      />

      <e-select
        v-model="language"
        :name="$tc('entity.user.fields.language')"
        dense
        :items="availableLocales"
      />

      <e-checkbox v-model="tos" vee-rules="required" class="align-center">
        <template #label>
          <span style="hyphens: auto" :class="{ 'body-2': $vuetify.breakpoint.xsOnly }">
            {{ $tc('views.auth.register.acceptTermsOfUse') }}
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
            to="#"
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

      <v-btn type="submit" color="primary" :disabled="!formComplete" block x-large>
        <v-progress-circular v-if="registering" indeterminate size="24" />
        <v-spacer />
        <span>{{ $tc('views.auth.register.register') }}</span>
        <v-spacer />
        <icon-spacer />
      </v-btn>
    </v-form>

    <p class="mt-8 mb-0 text--secondary text-center">
      {{ $tc('views.auth.register.alreadyHaveAnAccount') }}<br />
      <router-link :to="{ name: 'login' }">
        {{ $tc('views.auth.register.login') }}
      </router-link>
    </p>
  </auth-container>
</template>

<script>
import { load } from 'recaptcha-v3'
import AuthContainer from '@/components/layout/AuthContainer.vue'
import VueI18n from '@/plugins/i18n'

export default {
  name: 'Register',
  components: {
    AuthContainer,
  },
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
    formComplete() {
      return (
        this.tos &&
        this.firstname !== '' &&
        this.surname !== '' &&
        this.email !== '' &&
        this.pw1 !== '' &&
        this.pw2 !== '' &&
        this.pw1 === this.pw2
      )
    },
    formData() {
      return {
        firstname: this.firstname,
        surname: this.surname,
        email: this.email,
        password: this.pw1,
        language: this.language,
      }
    },
    pw2Rules() {
      return [(v) => (!!v && v) === this.pw1 || this.$tc('views.auth.register.mustMatch')]
    },
    availableLocales() {
      return VueI18n.availableLocales.map((l) => ({
        value: l,
        text: this.$tc('global.language', 1, l),
      }))
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

    if (window.environment.RECAPTCHA_SITE_KEY) {
      this.recaptcha = load(window.environment.RECAPTCHA_SITE_KEY, {
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
        .catch(() => (this.registering = false))
    },
  },
}
</script>
