<template>
  <v-container class="fill-height justify-center align-center" fluid>
    <v-col cols="12" sm="8" md="4">
      <v-card class="elevation-12">
        <v-toolbar color="green" dark elevation="1">
          <v-toolbar-title>Login</v-toolbar-title>
          <v-spacer />
          <v-btn color="green darken-3" :to="{ name: 'register' }">Register</v-btn>
        </v-toolbar>
        <v-card-text>
          <v-alert v-if="error" type="error">Login failed</v-alert>
          <v-form @submit.prevent="login">
            <v-text-field
              id="inputUsername"
              v-model="username"
              label="Username"
              name="username"
              prepend-icon="mdi-account"
              type="text" />

            <v-text-field
              id="inputPassword"
              v-model="password"
              label="Password"
              name="password"
              prepend-icon="mdi-lock"
              type="password" />
            <v-switch label="Remember me" />
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-menu offset-y>
            <template v-slot:activator="{ on }">
              <v-btn
                color="green"
                dark
                v-on="on">
                <v-progress-circular v-if="hitobitoLoggingIn" indeterminate
                                     size="14"
                                     class="mr-2" />
                Hitobito
              </v-btn>
            </template>
            <v-list>
              <v-list-item
                @click="loginPbsMiData">
                <v-list-item-icon class="mr-3">
                  <PbsMiDataLogo />
                </v-list-item-icon>
                <v-list-item-title>PBS MiData</v-list-item-title>
              </v-list-item>
              <v-list-item>
                <v-list-item-title>jubla.db</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
          <v-btn color="red" dark @click="loginGoogle">
            <v-progress-circular v-if="googleLoggingIn" indeterminate
                                 size="14"
                                 class="mr-2" />
            Google
          </v-btn>
          <v-btn color="primary" @click="login">
            <v-progress-circular v-if="normalLoggingIn" indeterminate
                                 size="14"
                                 class="mr-2" />
            Login
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-col>
  </v-container>
</template>

<script>
import { refreshLoginStatus } from '@/auth/auth'
import PbsMiDataLogo from '../../../public/pbsmidata.svg'

export default {
  name: 'Login',
  components: {
    PbsMiDataLogo
  },
  data () {
    return {
      username: '',
      password: '',
      error: false,
      normalLoggingIn: false,
      hitobitoLoggingIn: false,
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
