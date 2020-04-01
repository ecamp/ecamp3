<template>
  <auth-card>
    <h1 class="display-1 text-center">Anmelden</h1>
    <v-alert v-if="error" type="error">Login failed</v-alert>
    <v-form @submit.prevent="login">
      <v-text-field
        id="inputUsername"
        v-model="username"
        label="Benutzername"
        name="username"
        hide-details="auto"
        append-icon="mdi-account-outline"
        filled
        class="my-4"
        :dense="$vuetify.breakpoint.xsOnly"
        type="text" />

      <v-text-field
        id="inputPassword"
        v-model="password"
        label="Passwort"
        name="password"
        append-icon="mdi-lock-outline"
        filled
        hide-details="auto"
        class="my-4"
        :dense="$vuetify.breakpoint.xsOnly"
        type="password" />

      <v-btn color="primary" block
             :disabled="!(username && password)"
             :outlined="!(username && password)"
             :x-large="$vuetify.breakpoint.smAndUp"
             class="my-4"
             @click="login">
        <span>Anmelden</span>
        <v-spacer />
        <v-progress-circular v-if="normalLoggingIn" indeterminate size="24" />
      </v-btn>
    </v-form>
    <horizontal-rule label="oder" />
    <v-btn
      dark
      :x-large="$vuetify.breakpoint.smAndUp"
      color="green"
      outlined
      block
      class="my-4"
      @click="loginPbsMiData">
      <span class="text--secondary">Anmelden mit MiData</span>
      <v-spacer />
      <v-progress-circular v-if="hitobitoLoggingIn" indeterminate size="24" />
      <v-icon v-else :x-large="$vuetify.breakpoint.smAndUp">
        $vuetify.icons.pbs
      </v-icon>
    </v-btn>
    <v-btn dark
           color="blue-grey lighten-3"
           :x-large="$vuetify.breakpoint.smAndUp"
           block
           outlined
           class="my-4 text--secondary"
           @click="loginGoogle">
      <span class="text--secondary">Anmelden mit Google</span>
      <v-spacer />
      <v-progress-circular v-if="googleLoggingIn" indeterminate size="24" />
      <v-icon v-else>$vuetify.icons.google</v-icon>
    </v-btn>
    <p class="mt-8 mb-0 text--secondary text-center">
      Hast du noch keinen Account?<br>
      <router-link :to="{ name: 'register' }">Jetzt registrieren</router-link>
    </p>
  </auth-card>
</template>

<script>
import { refreshLoginStatus } from '@/plugins/auth'
import AuthCard from '@/components/layout/AuthCard'
import HorizontalRule from '@/components/layout/HorizontalRule'

export default {
  name: 'Login',
  components: {
    HorizontalRule,
    AuthCard
  },
  data () {
    return {
      username: '',
      password: '',
      error: false,
      normalLoggingIn: false,
      hitobitoLoggingIn: false,
      showCredits: true,
      googleLoggingIn: false
    }
  },
  beforeRouteEnter (to, from, next) {
    refreshLoginStatus(false).then(loggedIn => {
      if (loggedIn) {
        next(to.query.redirect || '/')
      } else {
        next()
      }
    })
  },
  methods: {
    async login () {
      this.normalLoggingIn = true
      this.error = false
      if (await this.$auth.login(this.username, this.password)) {
        this.redirect()
      } else {
        this.normalLoggingIn = false
        this.error = true
      }
    },
    async loginGoogle () {
      this.googleLoggingIn = true
      await this.$auth.loginGoogle()
      this.redirect()
    },
    async loginPbsMiData () {
      this.hitobitoLoggingIn = true
      await this.$auth.loginPbsMiData()
      this.redirect()
    },
    redirect () {
      this.$router.replace(this.$route.query.redirect || '/')
    }
  }
}
</script>

<style lang="scss" scoped>
  .btn--login {
    border: 1px solid;
  }
</style>
