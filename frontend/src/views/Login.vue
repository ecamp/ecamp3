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
import { isLoggedIn, login } from '@/auth'
export default {
  name: 'Login',
  data () {
    return {
      error: false
    }
  },
  beforeRouteUpdate (to, from, next) {
    if (isLoggedIn()) {
      this.redirect()
    }
    next()
  },
  methods: {
    loginGoogle () {
      login()
    },
    redirect () {
      this.$router.replace(this.$route.query.redirect || '/')
    }
  }
}
</script>

<style scoped>

</style>
