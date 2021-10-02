<template>
  <auth-container>
    <h1 class="display-1 text-center">{{ $tc('views.auth.login.title') }}</h1>

    <v-alert
      class="mt-2 text-justify"
      text
      dense
      border="left"
      style="hypens:auto"
      color="warning">
      <!-- eslint-disable-next-line vue/no-v-html -->
      <div v-html="$tc('views.auth.login.beta.notice')" />
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
        :label="$tc('views.auth.login.username')"
        name="username"
        append-icon="mdi-account-outline"
        :dense="$vuetify.breakpoint.xsOnly"
        type="text" />

      <e-text-field
        id="inputPassword"
        v-model="password"
        :label="$tc('views.auth.login.password')"
        name="password"
        append-icon="mdi-lock-outline"
        :dense="$vuetify.breakpoint.xsOnly"
        type="password" />

      <v-btn type="submit"
             :color="username && password ? 'blue darken-2' : 'blue lighten-4'" block
             :disabled="!(username && password)"
             outlined
             :x-large="$vuetify.breakpoint.smAndUp"
             class="my-4">
        <v-progress-circular v-if="normalLoggingIn" indeterminate size="24" />
        <v-icon v-else>$vuetify.icons.ecamp</v-icon>
        <v-spacer />
        <span>{{ $tc('views.auth.login.provider.ecamp') }}</span>
        <v-spacer />
        <icon-spacer />
      </v-btn>
    </v-form>
    <horizontal-rule :label="$tc('views.auth.login.or')" />
    <v-btn dark
           color="green"
           :x-large="$vuetify.breakpoint.smAndUp"
           block
           outlined
           class="my-4"
           @click="loginPbsMiData">
      <v-icon>$vuetify.icons.pbs</v-icon>
      <v-spacer />
      <span class="text--secondary">{{ $tc('views.auth.login.provider.midata') }}</span>
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
      <v-icon>$vuetify.icons.google</v-icon>
      <v-spacer />
      <span class="text--secondary">{{ $tc('views.auth.login.provider.google') }}</span>
      <v-spacer />
      <icon-spacer />
    </v-btn>
    <p class="mt-8 mb-0 text--secondary text-center">
      {{ $tc('views.auth.login.accountless') }}<br>
      <router-link :to="{ name: 'register' }">{{ $tc('views.auth.login.registernow') }}</router-link>
    </p>
  </auth-container>
</template>

<script>
import { isLoggedIn } from '@/plugins/auth'
import AuthContainer from '@/components/layout/AuthContainer.vue'
import HorizontalRule from '@/components/layout/HorizontalRule.vue'
import IconSpacer from '@/components/layout/IconSpacer.vue'

export default {
  name: 'Login',
  components: {
    IconSpacer,
    HorizontalRule,
    AuthContainer
  },
  beforeRouteEnter (to, from, next) {
    if (isLoggedIn()) {
      next(to.query.redirect || '/')
    } else {
      next()
    }
  },
  data () {
    return {
      username: '',
      password: '',
      error: false,
      normalLoggingIn: false,
      showCredits: true
    }
  },
  mounted () {
    this.$store.commit('setLanguage', this.$i18n.browserPreferredLocale)
  },
  methods: {
    async login () {
      this.normalLoggingIn = true
      this.error = false
      if (await this.$auth.login(this.username, this.password)) {
        this.$router.replace(this.$route.query.redirect || '/')
      } else {
        this.normalLoggingIn = false
        this.error = true
      }
    },
    async loginGoogle () {
      await this.$auth.loginGoogle()
    },
    async loginPbsMiData () {
      await this.$auth.loginPbsMiData()
    }
  }
}
</script>

<style lang="scss" scoped>
</style>
