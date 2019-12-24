<template>
  <section class="container">
    <h3>Login</h3>
    <p v-if="$route.query.redirect">
      You need to log in first.
    </p>
    <button
      class="btn btn-primary"
      @click="loginGoogle">
      Login with Google
    </button>
    <button
      class="btn btn-primary"
      @click="loginHitobito">
      Login with Hitobito
    </button>
    <p v-if="error">
      Error logging in.
    </p>
  </section>
</template>

<script>
import Vue from 'vue'
export default {
  name: 'Login',
  data () {
    return {
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
    loginGoogle () {
      // Make the login callback function available on global level, so the popup can call it
      window.loginSuccess = () => {
        this.$auth.loginSuccess()
        this.redirect()
      }
      const callbackUrl = window.location.origin + this.$router.resolve({ name: 'loginCallback' }).href
      this.$auth.login(callbackUrl)
    },
    loginHitobito () {
      // Make the login callback function available on global level, so the popup can call it
      window.loginSuccess = () => {
        this.$auth.loginSuccess()
        this.redirect()
      }
      const callbackUrl = window.location.origin + this.$router.resolve({ name: 'loginCallback' }).href
      this.$auth.login(callbackUrl)
    },
    redirect () {
      this.$router.replace(this.$route.query.redirect || '/')
    }
  }
}
</script>

<style scoped>

</style>
