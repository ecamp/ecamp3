<template>
  <auth-container>
    <div v-if="isProdSuffix && $vuetify.display.smAndDown" class="text-center">
      <v-icon size="64" icon="$ecamp" />
    </div>

    <h1 class="text-h4 text-center" :class="{ 'my-4': isProdSuffix }">
      {{ $t('global.button.login') }}
    </h1>

    <v-alert
      v-if="!isProdSuffix"
      class="mt-2 mb-4 text-justify text-warning"
      variant="tonal"
      border="start"
      density="compact"
      style="hyphens: auto"
      color="warning"
    >
      <div>
        <i18n-t :keypath="infoTextKey" scope="global">
          <template #br><br /></template>
        </i18n-t>
        <v-btn
          variant="text"
          elevation="0"
          height="32px"
          class="v-btn--has-bg float-end dev-login-button text-deep-orange-darken-4"
          append-icon="mdi-auto-fix"
          @click="
            (event) => {
              email = event.shiftKey ? 'admin@example.com' : 'test@example.com'
              password = 'test'
              login()
            }
          "
        >
          Login
        </v-btn>
      </div>
    </v-alert>
    <v-alert
      v-if="error"
      class="mt-2 mb-4"
      variant="tonal"
      border="start"
      type="error"
      icon="mdi-alert"
    >
      <span class="d-block">{{ error }}</span>
      <span class="d-block mt-1"
        >{{ $t('views.auth.login.passwordForgotten') }}
        <router-link :to="{ name: 'resetPasswordRequest' }">
          {{ $t('views.auth.login.resetPassword') }}
        </router-link>
      </span>
      <span class="d-block mt-1"
        >{{ $t('views.auth.login.notActivated') }}
        <router-link :to="{ name: 'resendActivation' }">
          {{ $t('views.auth.login.resendActivation') }}
        </router-link>
      </span>
    </v-alert>
    <v-form @submit.prevent="login">
      <e-text-field
        id="inputEmail"
        v-model="email"
        vee-rules="email|required"
        autofocus
        :label="$t('views.auth.login.email')"
        name="email"
        path="email"
        append-inner-icon="mdi-account-outline"
        :density="$vuetify.display.xs ? 'compact' : 'default'"
        type="email"
        autocomplete="username"
      />

      <e-password-field
        id="inputPassword"
        v-model="password"
        :label="$t('views.auth.login.password')"
        vee-rules="required"
        name="password"
        path="email"
        :density="$vuetify.display.xs ? 'compact' : 'default'"
        autocomplete="current-password"
      />
      <small class="ml-2">
        <router-link
          :to="{ name: 'resetPasswordRequest' }"
          tabindex="100"
          style="color: gray"
        >
          {{ $t('views.auth.login.passwordForgotten') }}
        </router-link>
      </small>

      <v-btn
        type="submit"
        :color="email && password ? 'blue-darken-2' : 'blue-lighten-4'"
        block
        :disabled="!(email && password) || authenticationInProgress"
        :size="$vuetify.display.smAndUp && 'large'"
        height="50"
        variant="outlined"
        class="my-4 pa-2 ec-login-button"
      >
        <v-progress-circular v-if="authenticationInProgress" indeterminate size="24" />
        <v-icon v-else size="large">$ecamp</v-icon>
        <v-spacer />
        <span>{{ $t('views.auth.login.provider.ecamp') }}</span>
        <v-spacer />
        <icon-spacer />
      </v-btn>
    </v-form>
    <horizontal-rule :label="$t('views.auth.login.or')" />
    <div class="openid-buttons">
      <v-btn
        dark
        color="#91697f"
        :size="$vuetify.display.smAndUp && 'x-large'"
        variant="text"
        @click="loginPbsMiData"
      >
        <v-icon class="my-1" color="#521d3a">$pbs</v-icon>
        <span class="text-grey-darken-2 text-body-2 font-weight-medium">{{
          $t('views.auth.login.provider.midata')
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
        <span class="text-grey-darken-2 text-body-2 font-weight-medium">{{
          $t('views.auth.login.provider.cevidb')
        }}</span>
      </v-btn>
      <v-btn
        dark
        color="blue"
        :size="$vuetify.display.smAndUp && 'x-large'"
        variant="text"
        class="jubla-btn"
        @click="loginJublaDB"
      >
        <v-icon size="24">$jubla</v-icon>
        <span class="text-grey-darken-2 text-body-2 font-weight-medium">{{
          $t('views.auth.login.provider.jubladb')
        }}</span>
      </v-btn>
      <v-btn
        dark
        :size="$vuetify.display.smAndUp && 'x-large'"
        color="blue-grey-lighten-3"
        variant="text"
        @click="loginGoogle"
      >
        <v-icon class="my-1">$google</v-icon>
        <span class="text-grey-darken-2 text-body-2 font-weight-medium">{{
          $t('views.auth.login.provider.google')
        }}</span>
      </v-btn>
      <small class="w-100">
        <i18n-t
          keypath="views.auth.login.acceptTermsOfServiceOnOAuthLogin"
          tag="p"
          class="text-grey-darken-2 text-center w-100 mt-2"
          scope="global"
          style="hyphens: auto"
        >
          <template #termsOfServiceLink>
            <a :href="termsOfServiceLink" target="_blank" style="color: gray">{{
              $t('views.auth.login.termsOfServiceLink')
            }}</a>
          </template>
        </i18n-t>
      </small>
    </div>
    <p class="mt-8 mb-0 text-grey-darken-2 text-center">
      {{ $t('views.auth.login.accountless') }}<br />
      <router-link :to="{ name: 'register' }" class="text-primary">
        {{ $t('views.auth.login.registernow') }}
      </router-link>
    </p>
  </auth-container>
</template>

<script>
import { isLoggedIn } from '@/plugins/auth'
import VueI18n, { componentI18n } from '@/plugins/i18n/index.js'
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
  head() {
    return {
      title: this.$t('global.button.login'),
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
    const navigatorLanguage = navigator.language
    if (componentI18n.availableLocales.includes(navigatorLanguage)) {
      this.$store.commit('setLanguage', navigatorLanguage)
    }
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
          if (VueI18n.global.availableLocales.includes(profile.language)) {
            await this.$store.commit('setLanguage', profile.language)
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

  :deep(.v-btn) {
    height: auto;
    min-width: auto;
    padding: 4px 0;
    flex-grow: 1;
  }

  :deep(.v-btn__content) {
    flex-direction: column;
  }
}
.dev-login-button {
  background-color: #f7e4cc !important;
  margin-top: 12px;
  margin-bottom: -12px;
  translate: 0 -12px;
}

/* eslint-disable-next-line vue-scoped-css/no-unused-selector */
.ec-login-button.v-btn--disabled {
  color: rgba(0, 0, 0, 0.26) !important;
  opacity: 1;
}

.ec-login-button :deep(.v-btn__overlay) {
  opacity: 0.08;
}

.ec-login-button :deep(.v-btn__content) {
  width: 100%;
}

.jubla-btn :deep(.v-btn__content) {
  height: 100%;
  justify-content: space-between;
}
</style>
