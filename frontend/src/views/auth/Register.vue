<template>
  <auth-container>
    <h1 class="display-1 text-center">Konto erstellen</h1>
    <v-form @submit.prevent="register">
      <e-text-field
        v-model="username"
        label="Username"
        name="username"
        append-icon="mdi-at"
        dense
        type="text"
        autofocus />

      <e-text-field
        v-model="firstname"
        label="Vorname"
        name="firstname"
        append-icon="mdi-account-outline"
        dense
        type="text" />

      <e-text-field
        v-model="surname"
        label="Nachname"
        name="surname"
        append-icon="mdi-account-outline"
        dense
        type="text" />

      <e-text-field
        v-model="email"
        label="eMail"
        name="email"
        append-icon="mdi-email-outline"
        dense
        type="text" />

      <e-text-field
        v-model="pw1"
        label="Password"
        name="password"
        :rules="pw1Rules"
        validate-on-blur
        append-icon="mdi-lock-outline"
        dense
        type="password" />

      <e-text-field
        v-model="pw2"
        label="Password erneut eingeben"
        name="password"
        :rules="pw2Rules"
        validate-on-blur
        dense
        append-icon="mdi-lock-outline"
        type="password" />

      <e-select
        v-model="language"
        :label="$tc('entity.user.fields.language')"
        name="language"
        dense
        :items="availableLocales" />

      <e-checkbox
        v-model="tos"
        required
        class="align-center">
        <template v-slot:label>
          <span style="hyphens: auto" :class="{'body-2':$vuetify.breakpoint.xsOnly}">
            Die Nutzungsbedingungen akzeptieren
          </span>
        </template>
        <template v-slot:append>
          <v-btn text min-width="0"
                 title="Öffnen"
                 target="_blank"
                 class="px-1"
                 to="#"
                 tabindex="-1">
            <v-icon small>mdi-open-in-new</v-icon>
          </v-btn>
        </template>
      </e-checkbox>
      <v-btn type="submit"
             color="primary"
             :disabled="!formComplete"
             block x-large>
        Register
      </v-btn>
    </v-form>
    <p class="mt-8 mb-0 text--secondary text-center">
      Du hast bereits einen Account?<br>
      <router-link :to="{ name: 'login' }">Anmelden</router-link>
    </p>
  </auth-container>
</template>

<script>
import AuthContainer from '@/components/layout/AuthContainer'
import VueI18n from '@/plugins/i18n'

export default {
  name: 'Register',
  components: {
    AuthContainer
  },
  data () {
    return {
      username: '',
      firstname: '',
      surname: '',
      email: '',
      pw1: '',
      pw2: '',
      language: '',
      tos: false
    }
  },
  computed: {
    formComplete () {
      return this.tos &&
        (this.username !== '') &&
        (this.firstname !== '') &&
        (this.surname !== '') &&
        (this.email !== '') &&
        (this.pw1 !== '') && (this.pw2 !== '') &&
        (this.pw1 === this.pw2)
    },
    formData () {
      return {
        username: this.username,
        firstname: this.firstname,
        surname: this.surname,
        email: this.email,
        password: this.pw1,
        language: this.language
      }
    },
    pw2Rules () {
      return [
        v => (!!v && v) === this.pw1 || 'Nicht übereinstimmend'
      ]
    },
    pw1Rules () {
      return [
        v => v.length >= 8 || 'Mindestens 8 Zeichen lang sein'
      ]
    },
    availableLocales () {
      return VueI18n.availableLocales.map(l => ({
        value: l,
        text: this.$tc('global.language', 1, l)
      }))
    }
  },
  watch: {
    language () {
      if (VueI18n.availableLocales.includes(this.language)) {
        this.$store.commit('setLanguage', this.language)
      }
    }
  },
  mounted () {
    const languages = navigator.languages || [navigator.language]
    for (const language of languages) {
      if (VueI18n.availableLocales.includes(language)) {
        this.language = language
        break
      }
    }
  },
  methods: {
    async register () {
      await this.$auth.register(this.formData)
      this.$router.push({ name: 'register-done' })
    }
  }
}
</script>

<style>

</style>
