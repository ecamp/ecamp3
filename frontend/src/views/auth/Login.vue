<template>
  <auth-container>
    <h1 class="display-1 text-center">{{ $tc('global.button.login') }}</h1>

    <v-alert
      class="mt-2 text-justify"
      text
      dense
      border="left"
      style="hypens: auto"
      color="warning"
    >
      <!-- eslint-disable-next-line vue/no-v-html -->
      <div v-html="$tc('views.auth.login.beta.notice')" />
    </v-alert>
    <v-alert v-if="error" outlined text border="left" type="error">
      {{ error }}
    </v-alert>
    <v-form @submit.prevent="login">
      <e-text-field
        id="inputEmail"
        v-model="email"
        autofocus
        :label="$tc('views.auth.login.email')"
        name="email"
        append-icon="mdi-account-outline"
        :dense="$vuetify.breakpoint.xsOnly"
        type="text"
      />

      <e-text-field
        id="inputPassword"
        v-model="password"
        :label="$tc('views.auth.login.password')"
        name="password"
        append-icon="mdi-lock-outline"
        :dense="$vuetify.breakpoint.xsOnly"
        type="password"
      />
      <small class="ml-2">
        <router-link
          :to="{ name: 'resetPasswordRequest' }"
          tabindex="100"
          style="color: gray"
        >
          {{ $tc('views.auth.login.passwordForgotten') }}
        </router-link>
      </small>

      <v-btn
        type="submit"
        :color="email && password ? 'blue darken-2' : 'blue lighten-4'"
        block
        :disabled="!(email && password) || authenticationInProgress"
        outlined
        :x-large="$vuetify.breakpoint.smAndUp"
        class="my-4"
      >
        <v-progress-circular v-if="authenticationInProgress" indeterminate size="24" />
        <v-icon v-else>$vuetify.icons.ecamp</v-icon>
        <v-spacer />
        <span>{{ $tc('views.auth.login.provider.ecamp') }}</span>
        <v-spacer />
        <icon-spacer />
      </v-btn>
    </v-form>
    <horizontal-rule :label="$tc('views.auth.login.or')" />
    <v-btn
      dark
      color="#91697f"
      :x-large="$vuetify.breakpoint.smAndUp"
      block
      outlined
      class="my-4"
      @click="loginPbsMiData"
    >
      <v-icon color="#521d3a">$vuetify.icons.pbs</v-icon>
      <v-spacer />
      <span class="text--secondary">{{ $tc('views.auth.login.provider.midata') }}</span>
      <v-spacer />
      <icon-spacer />
    </v-btn>
    <v-btn
      dark
      color="green"
      :x-large="$vuetify.breakpoint.smAndUp"
      block
      outlined
      class="my-4"
      @click="loginCeviDB"
    >
      <v-icon>$vuetify.icons.cevi</v-icon>
      <v-spacer />
      <span class="text--secondary">{{ $tc('views.auth.login.provider.cevidb') }}</span>
      <v-spacer />
      <icon-spacer />
    </v-btn>
    <v-btn
      dark
      color="blue-grey lighten-3"
      :x-large="$vuetify.breakpoint.smAndUp"
      block
      outlined
      class="my-4 text--secondary"
      @click="loginGoogle"
    >
      <v-icon>$vuetify.icons.google</v-icon>
      <v-spacer />
      <span class="text--secondary">{{ $tc('views.auth.login.provider.google') }}</span>
      <v-spacer />
      <icon-spacer />
    </v-btn>
    <p class="mt-8 mb-0 text--secondary text-center">
      {{ $tc('views.auth.login.accountless') }}<br />
      <router-link :to="{ name: 'register' }">
        {{ $tc('views.auth.login.registernow') }}
      </router-link>
    </p>
  </auth-container>
</template>

<script>
import { isLoggedIn } from '@/plugins/auth'
import AuthContainer from '@/components/layout/AuthContainer.vue'
import HorizontalRule from '@/components/layout/HorizontalRule.vue'
import IconSpacer from '@/components/layout/IconSpacer.vue'
import { serverErrorToString } from '@/helpers/serverError'

export default {
  name: 'Login',
  components: {
    IconSpacer,
    HorizontalRule,
    AuthContainer,
  },
  beforeRouteEnter(to, from, next) {
    if (isLoggedIn()) {
      next(to.query.redirect || '/')
    } else {
      next()
    }
  },
  data() {
    return {
      email: '',
      password: '',
      error: null,
      authenticationInProgress: false,
      showCredits: true,
    }
  },
  mounted() {
    this.$store.commit('setLanguage', this.$i18n.browserPreferredLocale)
  },
  methods: {
    async login() {
      this.authenticationInProgress = true
      this.error = null
      this.$auth
        .login(this.email, this.password)
        .then(() => {
          this.$router.replace(this.$route.query.redirect || '/')
        })
        .catch((e) => {
          this.authenticationInProgress = false
          this.error = serverErrorToString(e)
        })
    },
    async loginGoogle() {
      await this.$auth.loginGoogle()
    },
    async loginPbsMiData() {
      await this.$auth.loginPbsMiData()
    },
    async loginCeviDB() {
      await this.$auth.loginCeviDB()
    },
  },
}
</script>

<style lang="scss" scoped></style>
