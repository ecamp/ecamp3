<template>
  <form
    class="form-signin"
    method="post"
    action=""
    @submit.prevent="login">
    <div class="text-center mb-4">
      <i class="zmdi zmdi-hc-5x zmdi-labels" />
    </div>

    <div class="form-label-group">
      <input
        id="inputUsername"
        v-model="username"
        type="text"
        name="username"
        class="form-control"
        placeholder="Username"
        required
        autofocus>
      <label for="inputUsername">Username</label>
    </div>

    <div class="form-label-group">
      <input
        id="inputPassword"
        v-model="password"
        type="password"
        name="password"
        class="form-control"
        placeholder="Password"
        required>
      <label for="inputPassword">Password</label>
    </div>

    <div
      class="checkbox mb-3"
      style="margin-left: 10px">
      <label>
        <input
          type="checkbox"
          value="remember-me"> Remember me
      </label>
    </div>

    <div
      v-if="error"
      class="form-label-group">
      <div style="color:red">
        Login failed
      </div>
    </div>

    <div class="form-label-group">
      <button
        class="btn btn-lg btn-primary btn-block"
        type="submit">
        Sign in
      </button>
    </div>

    <hr>

    <div
      class="btn-group btn-block"
      style="margin-top: 7px">
      <a
        class="btn btn-link"
        style="width: 100%;"
        href="#"
        @click="loginGoogle">
        <i class="zmdi zmdi-google" />
      </a>
      <a
        class="btn btn-link"
        style="width: 100%;"
        href="">
        <i class="zmdi zmdi-facebook" />
      </a>
    </div>

    <hr style="margin-top: 22px">

    <div class="form-label-group">
      <router-link
        :to="{ name: 'register' }"
        class="btn btn-md btn-link btn-block">
        Register
      </router-link>
    </div>
  </form>
</template>

<script>
import Vue from 'vue'
export default {
  name: 'Login',
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
    login () {
      this.$auth.login(this.username, this.password).then(ok => {
        if (ok) {
          this.redirect()
        } else {
          this.error = true
        }
      })
    },
    loginGoogle () {
      // Make the login callback function available on global level, so the popup can call it
      window.loginSuccess = () => {
        this.$auth.loginSuccess()
        this.redirect()
      }
      const callbackUrl = window.location.origin + this.$router.resolve({ name: 'loginCallback' }).href
      this.$auth.loginGoogle(callbackUrl)
    },
    redirect () {
      this.$router.replace(this.$route.query.redirect || '/')
    }
  }
}
</script>

<style>

  :root {
    --input-padding-x: .75rem;
    --input-padding-y: .4rem;
  }

  #app {
    width: 100%;
    height: 100%;
  }
  .form-signin {
    width: 100%;
    max-width: 420px;
    padding: 15px;
    margin: 0 auto;
  }
  .form-label-group {
    position: relative;
    margin-bottom: 1rem;
  }
  .form-label-group > input,
  .form-label-group > label {
    padding: var(--input-padding-y) var(--input-padding-x);
  }
  .form-label-group > label {
    position: absolute;
    top: 0;
    left: 0;
    display: block;
    width: 100%;
    margin-bottom: 0; /* Override default `<label>` margin */
    line-height: 1.5;
    color: #495057;
    border: 1px solid transparent;
    border-radius: .25rem;
    transition: all .1s ease-in-out;
  }
  .form-label-group input::-webkit-input-placeholder {
    color: transparent;
  }
  .form-label-group input:-ms-input-placeholder {
    color: transparent;
  }
  .form-label-group input::-ms-input-placeholder {
    color: transparent;
  }
  .form-label-group input::-moz-placeholder {
    color: transparent;
  }
  .form-label-group input::placeholder {
    color: transparent;
  }
  .form-label-group input:not(:placeholder-shown) {
    padding-top: calc(var(--input-padding-y) + var(--input-padding-y) * (2 / 3));
    padding-bottom: calc(var(--input-padding-y) / 3);
  }
  .form-label-group input:not(:placeholder-shown) ~ label {
    padding-top: calc(var(--input-padding-y) / 4);
    padding-bottom: calc(var(--input-padding-y) / 4);
    font-size: 10px;
    color: #777;
  }
</style>
