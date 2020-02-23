<template>
  <v-content>
    <v-toolbar absolute width="100%"
               color="blue-grey darken-4" dark>
      <v-toolbar-title>
        <v-btn icon class="title">🏕</v-btn>
        eCamp
      </v-toolbar-title>
    </v-toolbar>
    <v-container class="fill-height align-center justify-center" fluid>
      <v-col cols="12" sm="8" md="4">
        <v-card class="elevation-12">
          <v-toolbar color="green" dark elevation="1">
            <v-toolbar-title>Register</v-toolbar-title>
            <v-spacer />
            <v-btn color="green darken-3" :to="{ name: 'login' }">Login</v-btn>
          </v-toolbar>
          <v-card-text>
            <v-form @submit.prevent="register">
              <v-text-field
                id="inputUsername"
                v-model="username"
                label="Username"
                name="username"
                prepend-icon="mdi-account"
                type="text" />

              <v-text-field
                id="inputEmail"
                v-model="email"
                label="eMail"
                name="email"
                prepend-icon="mdi-email"
                type="text" />

              <v-text-field
                id="inputPassword1"
                v-model="pw1"
                label="Password"
                name="password"
                :rules="pw1Rules"
                validate-on-blur
                prepend-icon="mdi-lock"
                type="password" />

              <v-text-field
                id="inputPassword2"
                v-model="pw2"
                label="Password erneut eingeben"
                name="password"
                :rules="pw2Rules"
                validate-on-blur
                prepend-icon="mdi-lock"
                type="password" />
            </v-form>
          </v-card-text>
          <v-card-actions>
            <v-spacer />
            <v-btn color="primary" :disabled="!formComplete" @click="register">
              Register
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-container>
  </v-content>
</template>

<script>
export default {
  name: 'Register',
  data () {
    return {
      username: '',
      email: '',
      pw1: '',
      pw2: ''
    }
  },
  computed: {
    formComplete () {
      return (this.username !== '') && (this.email !== '') &&
        (this.pw1 !== '') && (this.pw2 !== '') &&
        (this.pw1 === this.pw2)
    },
    formData () {
      return {
        username: this.username,
        email: this.email,
        password: this.pw1
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
