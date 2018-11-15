<template>
  <section class="container">
    <h3>Login</h3>
    <p v-if="$route.query.redirect">You need to log in first.</p>
    <button
      class="btn btn-primary"
      @click="loginGoogle">
      Login with Google
    </button>
    <p v-if="error">Error logging in.</p>
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
    if (Vue.auth.isLoggedIn()) {
      next(to.query.redirect || '/')
    }
    next()
  },
  methods: {
    loginGoogle () {
      // Make the login callback function available on global level, so the popup can call it
      window.loginSuccess = token => {
        this.$auth.loginSuccess(this, token)
        this.redirect()
      }
      this.$auth.login()
    },
    redirect () {
      this.$router.replace(this.$route.query.redirect || '/')
    }
  }
}
</script>

<style scoped>

</style>
