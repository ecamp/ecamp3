<template>
  <v-content>
    <v-toolbar absolute width="100%"
               color="blue-grey darken-4" dark>
      <v-toolbar-title>
        <v-btn icon class="title">🏕</v-btn>
        eCamp
      </v-toolbar-title>
    </v-toolbar>
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
            <v-btn color="red" dark @click="loginGoogle">Google</v-btn>
            <v-btn color="primary" @click="login">
              Login
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-container>
  </v-content>
</template>

<script>
import Vue from 'vue'
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
      error: false
    }
  },
  beforeRouteEnter (to, from, next) {
    Vue.auth.isLoggedIn().then(loggedIn => {
      if (loggedIn) {
        next(to.query.redirect || '/')
      } else {
        next()
      }
    })
  },
  methods: {
    async login () {
      if (await this.$auth.login(this.username, this.password)) {
        this.redirect()
      } else {
        this.error = true
      }
    },
    async loginGoogle () {
      await this.$auth.loginGoogle()
      this.redirect()
    },
    async loginPbsMiData () {
      await this.$auth.loginPbsMiData()
      this.redirect()
    },
    redirect () {
      this.$router.replace(this.$route.query.redirect || '/')
    }
  }
}
</script>
