<template>
  <div
    id="app"
    class="layout">
    <section class="layout-header container">
      <div
        class="btn-group float-right"
        style="margin-top: 7px">
        <router-link
          :to="{ name: 'home' }"
          class="btn btn-sm btn-primary">
          Home
        </router-link>
        <router-link
          :to="{ name: 'camps', params: { groupName: 'Pfadi Bewegung Schweiz' } }"
          class="btn btn-sm btn-primary d-none d-md-block">
          Camps
        </router-link>
        <router-link
          v-if="loggedIn === false"
          :to="{ name: 'login' }"
          class="btn btn-sm btn-primary d-md-block">
          Log in
        </router-link>
        <router-link
          v-else-if="loggedIn === true"
          :to="{ name: 'logout' }"
          class="btn btn-sm btn-primary d-md-block">
          Log out
        </router-link>
      </div>
      <h1>eCamp3</h1>
      <hr>
    </section>
    <router-view />
  </div>
</template>
<script>
export default {
  name: 'App',
  data () {
    return {
      loggedIn: null
    }
  },
  created () {
    this.$auth.subscribe(this.checkLoginStatus)
    this.checkLoginStatus()
  },
  methods: {
    async checkLoginStatus () {
      this.loggedIn = await this.$auth.isLoggedIn()
    }
  }
}
</script>
<style lang="scss">
  @import '../node_modules/bootstrap/scss/bootstrap.scss';
</style>
