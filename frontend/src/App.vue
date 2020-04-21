<template>
  <v-app>
    <router-view name="navigation" />

    <router-view name="aside" />

    <!-- main content -->
    <v-content>
      <router-view />
    </v-content>

    <!-- footer -->
    <v-footer v-if="$vuetify.breakpoint.smAndUp"
              app color="grey lighten-5">
      <small>eCamp v0.1.0</small>
      <v-spacer />
      <language-switcher />
    </v-footer>
  </v-app>
</template>

<script>
import LanguageSwitcher from '@/components/layout/LanguageSwitcher'
export default {
  name: 'App',
  components: { LanguageSwitcher },
  mounted () {
    this.api.get()
    const lang = 'de' // TODO save this value to database?
    this.$root.$i18n.locale = lang
    this.axios.defaults.headers.common['Accept-Language'] = lang
    document.querySelector('html').setAttribute('lang', lang)
  }
}
</script>
<style lang="scss">
  @import "src/scss/global";
  // consider replacing with CDN for production
  @import '../node_modules/@mdi/font/css/materialdesignicons.css';

  .v-btn.ec-drawer-collapse {
    right: 0;
  }

  .v-content {
    height: 100vh;
    position: relative;
  }

  .v-app-bar .v-toolbar__content {
    padding-left: 0;
    padding-right: 0;
    width: 100%;
  }

  .v-content__wrap {
    overflow: auto;
    position: static;
  }

  .user-nav {
    border-top-left-radius: 0!important;
    border-top-right-radius: 0!important;
  }

  .v-navigation-drawer--temporary.v-navigation-drawer--clipped {
    margin-top: 56px;
  }

  .v-btn--open {
    background: #B0BEC5 !important;
    color: rgba(0, 0, 0, 0.87) !important;
  }

  .ec-usermenu {
    border-top-left-radius: 0 !important;
    border-top-right-radius: 0 !important;
    right: 0;
    left: inherit !important;

    .v-list {
      border-radius: 0;
    }
  }

  .v-app-bar .v-toolbar__content {
    padding-left: 0;
    padding-right: 0;
    width: 100%;
  }

  .v-navigation-drawer__content .v-card {
    border-top-left-radius: 0 !important;
    border-top-right-radius: 0 !important;
  }

  @media #{map-get($display-breakpoints, 'xs-only')}{
    .v-content .container {
      min-height: 100%;
      display: flex;

      .v-card {
        margin-left: 0 !important;
        margin-right: 0 !important;
        flex: auto;
      }
    }
  }

  .ec-menu-left {
    left: 0 !important;
    font-feature-settings: 'tnum';
  }

  @media #{map-get($display-breakpoints, 'sm-and-down')}{
    .container.container--fluid {
      padding: 0;

      & > .v-card {
        border-radius: 0;
      }
    }
    .sr-only-sm-and-down {
      position: absolute;
      width: 1px;
      height: 1px;
      padding: 0;
      overflow: hidden;
      clip: rect(0, 0, 0, 0);
      white-space: nowrap;
      clip-path: inset(50%);
      border: 0;
    }
  }
</style>
