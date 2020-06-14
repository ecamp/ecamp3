<template>
  <auth-container>
    <h1 class="display-1 text-center">{{ $t('login.title') }}</h1>

    <v-alert
      class="mt-2 text-justify"
      text
      dense
      border="left"
      style="hypens:auto"
      color="warning">
      <div v-html="$t('login.beta.notice')" />
    </v-alert>
    <v-alert v-if="error"
             outlined
             text
             border="left"
             type="error">
      Login failed
    </v-alert>
    <v-form @submit.prevent="login">
      <e-text-field
        id="inputUsername"
        v-model="username"
        :label="$t('login.username')"
        name="username"
        append-icon="mdi-account-outline"
        :dense="$vuetify.breakpoint.xsOnly"
        type="text" />

      <e-text-field
        id="inputPassword"
        v-model="password"
        :label="$t('login.password')"
        name="password"
        append-icon="mdi-lock-outline"
        :dense="$vuetify.breakpoint.xsOnly"
        type="password" />

      <v-btn :color="username && password ? 'blue darken-2' : 'blue lighten-4'" block
             :disabled="!(username && password)"
             outlined
             :x-large="$vuetify.breakpoint.smAndUp"
             class="my-4"
             @click="login">
        <v-progress-circular v-if="normalLoggingIn" indeterminate size="24" />
        <v-icon v-else>$vuetify.icons.ecamp</v-icon>
        <v-spacer />
        <span>{{ $t('login.provider.ecamp') }}</span>
        <v-spacer />
        <icon-spacer />
      </v-btn>
    </v-form>
    <horizontal-rule :label="$t('login.or')" />
    <v-btn
      dark
      :x-large="$vuetify.breakpoint.smAndUp"
      color="green"
      outlined
      block
      class="my-4"
      @click="loginPbsMiData">
      <v-progress-circular v-if="hitobitoLoggingIn" indeterminate size="24" />
      <v-icon v-else>$vuetify.icons.pbs</v-icon>
      <v-spacer />
      <span class="text--secondary">{{ $t('login.provider.midata') }}</span>
      <v-spacer />
      <icon-spacer />
    </v-btn>
    <v-btn dark
           color="blue-grey lighten-3"
           :x-large="$vuetify.breakpoint.smAndUp"
           block
           outlined
           class="my-4 text--secondary"
           @click="loginGoogle">
      <v-progress-circular v-if="googleLoggingIn" indeterminate size="24" />
      <v-icon v-else>$vuetify.icons.google</v-icon>
      <v-spacer />
      <span class="text--secondary">{{ $t('login.provider.google') }}</span>
      <v-spacer />
      <icon-spacer />
    </v-btn>
    <p class="mt-8 mb-0 text--secondary text-center">
      {{ $t('login.accountless') }}<br>
      <router-link :to="{ name: 'register' }">{{ $t('login.registernow') }}</router-link>
    </p>
  </auth-container>
</template>

<script>
import { refreshLoginStatus } from '@/plugins/auth'
import AuthContainer from '@/components/layout/AuthContainer'
import HorizontalRule from '@/components/layout/HorizontalRule'
import IconSpacer from '@/components/layout/IconSpacer'

export default {
  name: 'Login',
  components: {
    IconSpacer,
    HorizontalRule,
    AuthContainer
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
</style>
