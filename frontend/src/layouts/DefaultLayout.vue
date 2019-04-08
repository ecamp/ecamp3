<template>
  <div class="ecamp_layout">
    <header class="ecamp_header">
      <nav class="">
        <b-navbar tag="section" class="ecamp_navbar_main mb-0 shadow-sm" type="dark" variant="dark" toggleable="md">
          <b-navbar-brand :to="{ name: 'camps', params: { groupName: 'Pfadi Bewegung Schweiz' } }">
            <i>🏕</i>️ <span class="d-none d-sm-inline">eCamp</span><span class="d-none d-sm-inline d-md-none text-muted"> – </span><span class="d-md-none text-muted">Sola 2018</span>
          </b-navbar-brand>
          <b-navbar-toggle target="#main_navigation"></b-navbar-toggle>
          <b-collapse id="main_navigation" is-nav>
            <b-navbar-nav class="mr-auto">
              <b-nav-item
                :to="{ name: 'home' }">
                Home
              </b-nav-item>
              <b-nav-item
                :to="{ name: 'camps', params: { groupName: 'Pfadi Bewegung Schweiz' } }">
                Camps
              </b-nav-item>
            </b-navbar-nav>

            <b-navbar-nav>
              <b-nav-item
                v-if="loggedIn === false"
                :to="{ name: 'login' }">
                Log in
              </b-nav-item>
              <b-nav-item
                v-else-if="loggedIn === true"
                :to="{ name: 'logout' }">
                Log out
              </b-nav-item>
            </b-navbar-nav>
          </b-collapse>
        </b-navbar>

        <b-navbar tag="section" class="ecamp_navbar_second mb-0 shadow-sm" variant="white" toggleable="md">
          <b-collapse id="second_navigation" is-nav>
            <b-navbar-brand>Sola 2019</b-navbar-brand>
            <b-navbar-nav class="flex-row mr-auto">
              <b-nav-item href="#" class="flex-grow-1 text-center">
                Test
              </b-nav-item>
            </b-navbar-nav>
          </b-collapse>
        </b-navbar>
      </nav>
    </header>
    <section class="ecamp_main">
      <b-collapse visible tag="aside" class="ecamp_aside col-12 col-md-5 col-lg-4 width" id="aside">
        <div class="collapse-inner">
          <router-view name="aside"/>
        </div>
      </b-collapse>
      <button v-b-toggle.aside class="ecamp_aside-hide d-none d-md-flex"></button>
      <main class="ecamp_content">
        <router-view/>
      </main>
    </section>
    <footer class="ecamp_footer bg-light p-1">
      v0.0.1
    </footer>
  </div>
</template>

<script>
export default {
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
</style>
