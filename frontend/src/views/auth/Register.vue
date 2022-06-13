<template>
  <auth-container>
    <h1 class="display-1 text-center">{{ $tc('views.auth.register.title') }}</h1>
    <v-form @submit.prevent="register">
      <e-text-field
        v-model="username"
        :label="$tc('entity.user.fields.username')"
        name="username"
        append-icon="mdi-account-outline"
        dense
        type="text"
        autofocus
      />

      <e-text-field
        v-model="firstname"
        :label="$tc('entity.user.fields.firstname')"
        name="firstname"
        append-icon="mdi-account-outline"
        dense
        type="text"
      />

      <e-text-field
        v-model="surname"
        :label="$tc('entity.user.fields.surname')"
        name="surname"
        append-icon="mdi-account-outline"
        dense
        type="text"
      />

      <e-text-field
        v-model="email"
        :label="$tc('entity.user.fields.email')"
        name="Email"
        vee-rules="email"
        append-icon="mdi-at"
        dense
        type="text"
      />

      <e-text-field
        v-model="pw1"
        :label="$tc('entity.user.fields.password')"
        name="password"
        :rules="pw1Rules"
        validate-on-blur
        append-icon="mdi-lock-outline"
        dense
        type="password"
      />

      <e-text-field
        v-model="pw2"
        :label="$tc('views.auth.register.passwordConfirmation')"
        name="password"
        :rules="pw2Rules"
        validate-on-blur
        dense
        append-icon="mdi-lock-outline"
        type="password"
      />

      <e-select
        v-model="language"
        :label="$tc('entity.user.fields.language')"
        name="language"
        dense
        :items="availableLocales"
      />

      <e-checkbox v-model="tos" required class="align-center">
        <template #label>
          <span style="hyphens: auto" :class="{ 'body-2': $vuetify.breakpoint.xsOnly }">
            {{ $tc('views.auth.register.acceptTermsOfUse') }}
          </span>
        </template>
        <template #append>
          <v-btn
            text
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
      <v-btn type="submit" color="primary" :disabled="!formComplete" block x-large>
        {{ $tc('views.auth.register.register') }}
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
      username: '',
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
        this.username !== '' &&
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
        username: this.username,
        firstname: this.firstname,
        surname: this.surname,
        email: this.email,
        password: this.pw1,
        language: this.language,
      }
    },
    pw2Rules() {
      return [(v) => (!!v && v) === this.pw1 || 'Nicht übereinstimmend']
    },
    pw1Rules() {
      return [(v) => v.length >= 8 || 'Mindestens 8 Zeichen lang sein']
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
      let recaptchaToken = null
      if (this.recaptcha) {
        const recaptcha = await this.recaptcha
        recaptchaToken = await recaptcha.execute('login')
      }

      await this.$auth.register({
        password: this.formData.password,
        profile: {
          username: this.formData.username,
          firstname: this.formData.firstname,
          surname: this.formData.surname,
          email: this.formData.email,
          language: this.formData.language,
        },
        recaptchaToken: recaptchaToken,
      })
      this.$router.push({ name: 'register-done' })
    },
  },
}
</script>

<style></style>
