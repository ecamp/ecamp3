<template>
  <form
    class="form-register"
    method="post"
    action=""
    @submit.prevent="register">
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
        id="inputEmail"
        v-model="email"
        type="text"
        name="username"
        class="form-control"
        placeholder="eMail"
        required
        autofocus>
      <label for="inputEmail">eMail</label>
    </div>

    <div class="form-label-group">
      <input
        id="inputPassword1"
        v-model="pw1"
        type="password"
        name="password"
        class="form-control"
        placeholder="Password"
        required>
      <label for="inputPassword1">Password</label>
    </div>

    <div class="form-label-group">
      <input
        id="inputPassword2"
        v-model="pw2"
        type="password"
        name="password"
        class="form-control"
        placeholder="Password"
        required>
      <label for="inputPassword2">Password</label>
    </div>

    <div v-if="pwFeedback === 'ok'">
      pw ok
    </div>
    <div v-if="pwFeedback === 'nok'">
      pw error
    </div>

    <div class="form-label-group">
      <button
        class="btn btn-lg btn-primary btn-block"
        type="submit"
        :disabled="!formComplete">
        Register
      </button>
    </div>

    <hr style="margin-top: 40px">

    <div class="form-label-group">
      <router-link
        :to="{ name: 'login' }"
        class="btn btn-md btn-link btn-block">
        Login
      </router-link>
    </div>
  </form>
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
    pwFeedback () {
      if (this.pw1 !== '' && this.pw2 !== '') {
        return (this.pw1 === this.pw2) ? 'ok' : 'nok'
      }
      return ''
    },
    formComplete () {
      return (this.username !== '') && (this.email !== '') &&
        (this.pw1 !== '') && (this.pw2 !== '') &&
        (this.pw1 === this.pw2)
    },
    formData () {
      return {
        username: this.username,
        email: this.email,
        pw: this.pw1
      }
    }
  },
  methods: {
    async register () {
      let url = process.env.VUE_APP_ROOT_API + '/register'
      let res = (await this.axios.post(url, this.formData))

      if (res.status === 200) {
        console.log(res.data)
        this.$router.push({ name: 'register-done' })
      } else {
        console.warn(res.data)
      }
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
    .form-register {
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
