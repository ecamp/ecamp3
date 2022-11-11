<template>
  <v-app>
    <router-view name="navigation" />

    <router-view name="aside" />

    <!-- main content -->
    <v-main>
      <router-view />
    </v-main>

    <!-- footer -->
    <v-footer v-if="$vuetify.breakpoint.smAndUp" app color="grey lighten-5">
      <small
        >eCamp
        <a v-if="version" :href="versionLink" target="_blank">
          {{ version }}
        </a>
        <span class="ml-1">{{ deploymentTime }}</span></small
      >
      <v-spacer />
      <language-switcher v-if="isDev" />
    </v-footer>
  </v-app>
</template>

<script>
import LanguageSwitcher from '@/components/layout/LanguageSwitcher.vue'
import VueI18n from '@/plugins/i18n'
import { parseTemplate } from 'url-template'

export default {
  name: 'App',
  components: { LanguageSwitcher },
  computed: {
    profile() {
      return this.$store.state.auth.user
    },
    deploymentTime() {
      const timestamp = window.environment.DEPLOYMENT_TIME
      const dateTime = timestamp ? this.$date.unix(timestamp) : this.$date()
      return dateTime.format(this.$tc('global.datetime.dateTimeLong'))
    },
    version() {
      return window.environment.VERSION || ''
    },
    versionLink() {
      return (
        parseTemplate(window.environment.VERSION_LINK_TEMPLATE).expand({
          version: this.version,
        }) || '#'
      )
    },
    isDev() {
      return window.environment.FEATURE_DEVELOPER ?? false
    },
  },
  created() {
    this.$store.commit('setLanguage', this.$store.state.lang.language)
  },
  async mounted() {
    if (this.$auth.isLoggedIn()) {
      const user = await this.$auth.loadUser()
      const profile = await user.profile()._meta.load

      if (VueI18n.availableLocales.includes(profile.language)) {
        this.$store.commit('setLanguage', profile.language)
      }
    }
  },
}
</script>
<style lang="scss">
@import 'src/scss/global';
@import '~@mdi/font/css/materialdesignicons.css';

.v-btn.ec-drawer-open,
.v-btn.ec-drawer-collapse {
  position: absolute;
  right: 0;
  bottom: 0;
}

@media #{map-get($display-breakpoints, 'xs-only')} {
  html,
  body,
  .v-application {
    height: 100%;
  }

  .v-application--wrap {
    min-height: 100% !important;
  }
}

.v-app-bar .v-toolbar__content {
  padding-left: 0;
  padding-right: 0;
  width: 100%;
}

.user-nav {
  border-top-left-radius: 0 !important;
  border-top-right-radius: 0 !important;
}

.v-btn--open {
  background: #b0bec5 !important;
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

@media #{map-get($display-breakpoints, 'xs-only')} {
  .v-main > .v-main__wrap > .container {
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

@media #{map-get($display-breakpoints, 'sm-and-down')} {
  // TODO: this changes look & feel of all v-containers. Do we really want this?
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
