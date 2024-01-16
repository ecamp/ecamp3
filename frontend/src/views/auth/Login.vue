<template>
  <auth-container>
    <div v-if="isProdSuffix && $vuetify.display.smAndDown" class="text-center">
      <v-icon size="64"> $ecamp </v-icon>
    </div>

    <h1 class="text-h4 text-center" :class="{ 'my-4': isProdSuffix }">
      {{ $tc('global.button.login') }}
    </h1>

    <v-alert
      v-if="!isProdSuffix"
      class="mt-2 text-justify"
      text
      density="compact"
      border="start"
      style="hypens: auto"
      color="warning"
    >
      <div>
        <i18n :path="infoTextKey">
          <template #br><br /></template>
        </i18n>
      </div>
    </v-alert>
    <v-alert v-if="error" variant="outlined" text border="start" type="error">
      {{ error }}
    </v-alert>
    <v-form @submit.prevent="login">
      <e-text-field
        id="inputEmail"
        v-model="email"
        vee-rules="email|required"
        autofocus
        :label="$tc('views.auth.login.email')"
        name="email"
        append-icon="mdi-account-outline"
        :dense="$vuetify.display.xsOnly"
        type="email"
        autocomplete="username"
      />

      <e-text-field
        id="inputPassword"
        v-model="password"
        :label="$tc('views.auth.login.password')"
        vee-rules="required"
        name="password"
        append-icon="mdi-lock-outline"
        :dense="$vuetify.display.xsOnly"
        type="password"
        autocomplete="current-password"
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
        variant="outlined"
        :size="$vuetify.display.smAndUp && 'x-large'"
        class="my-4"
      >
        <v-progress-circular v-if="authenticationInProgress" indeterminate size="24" />
        <v-icon v-else>$ecamp</v-icon>
        <v-spacer />
        <span>{{ $tc('views.auth.login.provider.ecamp') }}</span>
        <v-spacer />
        <icon-spacer />
      </v-btn>
    </v-form>
    <horizontal-rule :label="$tc('views.auth.login.or')" />
    <div class="openid-buttons">
      <v-btn
        dark
        color="#91697f"
        :size="$vuetify.display.smAndUp && 'x-large'"
        variant="text"
        @click="loginPbsMiData"
      >
        <v-icon class="my-1" color="#521d3a">$pbs</v-icon>
        <span class="text--secondary text-body-2 font-weight-medium">{{
          $tc('views.auth.login.provider.midata')
        }}</span>
      </v-btn>
      <v-btn
        dark
        color="green"
        :size="$vuetify.display.smAndUp && 'x-large'"
        variant="text"
        @click="loginCeviDB"
      >
        <v-icon class="my-1">$cevi</v-icon>
        <span class="text--secondary text-body-2 font-weight-medium">{{
          $tc('views.auth.login.provider.cevidb')
        }}</span>
      </v-btn>
      <v-btn
        dark
        color="blue"
        :size="$vuetify.display.smAndUp && 'x-large'"
        variant="text"
        @click="loginJublaDB"
      >
        <v-icon size="32">$jubla</v-icon>
        <span class="text--secondary text-body-2 font-weight-medium">{{
          $tc('views.auth.login.provider.jubladb')
        }}</span>
      </v-btn>
      <v-btn
        dark
        color="blue-grey-lighten-3"
        :size="$vuetify.display.smAndUp && 'x-large'"
        variant="text"
        @click="loginGoogle"
      >
        <v-icon class="my-1">$google</v-icon>
        <span class="text--secondary text-body-2 font-weight-medium">{{
          $tc('views.auth.login.provider.google')
        }}</span>
      </v-btn>
      <small class="w-100">
        <i18n
          path="views.auth.login.acceptTermsOfServiceOnOAuthLogin"
          tag="p"
          class="text--secondary text-center w-100 mt-2"
          style="hyphens: auto"
        >
          <template #termsOfServiceLink>
            <a :href="termsOfServiceLink" target="_blank" style="color: gray">{{
              $tc('views.auth.login.termsOfServiceLink')
            }}</a>
          </template>
        </i18n>
      </small>
    </div>
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
import VueI18n from '@/plugins/i18n/index.js'
import AuthContainer from '@/components/layout/AuthContainer.vue'
import HorizontalRule from '@/components/layout/HorizontalRule.vue'
import IconSpacer from '@/components/layout/IconSpacer.vue'
import { serverErrorToString } from '@/helpers/serverError'
import { parseTemplate } from 'url-template'
import { getEnv } from '@/environment.js'

const LOGIN_INFO_TEXT_KEY = getEnv().LOGIN_INFO_TEXT_KEY

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
  computed: {
    infoTextSuffix() {
      return LOGIN_INFO_TEXT_KEY
    },
    isProdSuffix() {
      return this.infoTextSuffix === 'prod'
    },
    infoTextKey() {
      return `views.auth.login.infoText.${this.infoTextSuffix ?? 'prod'}`
    },
    termsOfServiceLink() {
      return (
        parseTemplate(getEnv().TERMS_OF_SERVICE_LINK_TEMPLATE || '').expand({
          lang: this.$store.state.lang.language.substring(0, 2),
        }) || false
      )
    },
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
        .then(async () => {
          const user = await this.$auth.loadUser()
          const profile = await user.profile()._meta.load
          if (VueI18n.availableLocales.includes(profile.language)) {
            // await this.$store.commit('setLanguage', profile.language)
          }
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
    async loginJublaDB() {
      await this.$auth.loginJublaDB()
    },
  },
}
</script>

<style lang="scss" scoped>
.openid-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;

  :deep .v-btn {
    height: auto;
    min-width: auto;
    padding: 4px 0;
    flex-grow: 1;
  }

  :deep .v-btn__content {
    flex-direction: column;
  }
}
</style>
