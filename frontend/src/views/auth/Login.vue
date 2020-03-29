<template>
  <v-container class="sso--container fill-height justify-center align-center" fluid>
    <v-col cols="12" sm="8"
           md="5" lg="4"
           class="pa-0 pa-sm-4"
           :class="{'fill-height': $vuetify.breakpoint.xsOnly}">
      <content-card class="pa-8"
                    :class="{'fill-height': $vuetify.breakpoint.xsOnly}">
        <h1 class="display-1 text-center">Anmelden</h1>
        <v-alert v-if="error" type="error">Login failed</v-alert>
        <v-form @submit.prevent="login">
          <v-text-field
            id="inputUsername"
            v-model="username"
            label="Benutzername"
            name="username"
            hide-details="auto"
            append-icon="mdi-account"
            filled
            class="my-4"
            :dense="$vuetify.breakpoint.xsOnly"
            type="text" />

          <v-text-field
            id="inputPassword"
            v-model="password"
            label="Passwort"
            name="password"
            append-icon="mdi-lock"
            filled
            hide-details="auto"
            class="my-4"
            :dense="$vuetify.breakpoint.xsOnly"
            type="password" />
        </v-form>

        <v-btn color="primary" block
               :disabled="!(username && password)"
               :x-large="$vuetify.breakpoint.smAndUp"
               class="my-4"
               @click="login">
          <v-progress-circular v-if="normalLoggingIn" indeterminate
                               size="14"
                               class="mr-2" />
          <span>Anmelden</span>
          <v-spacer />
        </v-btn>
        <hr class="">
        <v-btn
          dark
          :x-large="$vuetify.breakpoint.smAndUp"
          color="green"
          outlined
          block
          class="my-4"
          v-on="on" @click="loginPbsMiData">
          <span class="text--secondary">Anmelden mit MiData</span>
          <v-spacer />
          <v-icon
            :x-large="$vuetify.breakpoint.smAndUp">$vuetify.icons.pbs
          </v-icon>
          <v-progress-circular v-if="hitobitoLoggingIn" indeterminate
                               size="14"
                               class="mr-2" />
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
          <v-icon>$vuetify.icons.google</v-icon>
          <v-progress-circular v-if="googleLoggingIn" indeterminate
                               size="14"
                               class="mr-2" />
        </v-btn>
        <p class="mt-8 mb-0 text--secondary text-center">
          Hast du noch keinen Account?<br />
          <router-link :to="{ name: 'register' }">Jetzt registrieren</router-link>
        </p>
      </content-card>
    </v-col>
    <photo-credit href="https://liveit.ch/author/manuel-lopez/">
      Photo by Manuel Lopez / Inox
    </photo-credit>
  </v-container>
</template>

<script>
import { refreshLoginStatus } from '@/plugins/auth'
import ContentCard from '@/components/layout/ContentCard'
import PhotoCredit from '@/components/layout/PhotoCredit'

export default {
  name: 'Login',
  components: { PhotoCredit, ContentCard },
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
  computed: {
    width () {
      switch (this.$vuetify.breakpoint.name) {
        case 'xs':
          return '100%'
        case 'sm':
          return '420px'
        case 'md':
          return '450px'
        default:
          return '500px'
      }
    }
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
