<template>
  <div class="ecamp_layout">
    <header class="ecamp_header">
      <nav class="">
        <b-navbar
          tag="section"
          class="ecamp_navbar_main mb-0 shadow-sm"
          type="dark"
          variant="dark"
          toggleable="md">
          <b-navbar-brand :to="{ name: 'camps', params: { groupName: 'Pfadi Bewegung Schweiz' } }">
            <i>🏕</i>️ <span class="d-none d-sm-inline">eCamp</span><span class="d-none d-sm-inline d-md-none text-muted"> – </span><span class="d-md-none text-muted">Sola 2018</span>
          </b-navbar-brand>
          <b-navbar-toggle target="#main_navigation" />
          <b-collapse
            id="main_navigation"
            is-nav>
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

        <b-navbar
          tag="section"
          class="ecamp_navbar_second mb-0 shadow-sm"
          variant="white"
          toggleable="md">
          <b-collapse
            id="second_navigation"
            is-nav>
            <b-navbar-brand>Sola 2019</b-navbar-brand>
            <b-navbar-nav class="flex-row mr-auto">
              <b-nav-item
                href="#"
                class="flex-grow-1 text-center">
                Test
              </b-nav-item>
            </b-navbar-nav>
          </b-collapse>
        </b-navbar>
      </nav>
    </header>
    <section class="ecamp_main">
      <b-collapse
        id="aside"
        visible
        tag="aside"
        class="ecamp_aside col-12 col-md-5 col-lg-4 width">
        <div class="collapse-inner">
          <router-view name="aside" />
        </div>
      </b-collapse>
      <button
        v-b-toggle.aside
        class="ecamp_aside-hide d-none d-md-flex" />
      <main class="ecamp_content">
        <router-view />
      </main>
    </section>
    <footer class="ecamp_footer bg-light p-1">
      v0.0.1
    </footer>
  </div>
</template>

<script>
import { BCollapse, BNavbar, BNavbarBrand, BNavbarToggle, BNavbarNav, BNavItem } from 'bootstrap-vue'

export default {
  components: { BNavbarNav, BNavbarToggle, BNavbar, BNavbarBrand, BCollapse, BNavItem },
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
  .ecamp_layout {
    display: flex;
    flex-direction: column;
    height: 100vh;
    background: #90A4AE;
  }

  .ecamp_navbar_main {
    z-index: 110;
  }

  .ecamp_navbar_second {
    z-index: 100;
  }

  .ecamp_main {
    display: flex;
    flex-wrap: wrap;
    flex-grow: 1;
  }

  .ecamp_aside {
    background: #dee3e8;
  }

  .ecamp_aside-hide {
    margin: 0;

    :before {
      display: block;
    }
    &[aria-expanded="true"]:before {
      content: '◀';
    }

    &[aria-expanded="false"]:before {
      content: '▶️';
    }
  }

  .ecamp_content {
    flex: 1 1 0;
  }

  .collapsing.width {
    transition-property: visibility, flex, max-width;
    flex: 0 0 0;
    max-width: 0;
    height: auto;
  }
</style>
