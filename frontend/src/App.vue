<template>
  <v-app>
    <router-view name="navigation" />

    <router-view name="aside" />

    <!-- main content -->
    <v-main>
      <router-view />
    </v-main>

    <v-footer v-if="offline" app>
      <p class="mb-0">
        <strong>{{ $tc('global.info.offline.title') }}</strong>
        {{ $tc('global.info.offline.description') }}
      </p>
    </v-footer>
  </v-app>
</template>

<script>
import VueI18n from '@/plugins/i18n'

export default {
  name: 'App',
  data: () => ({
    offline: false,
  }),
  created() {
    this.$store.commit('setLanguage', this.$store.state.lang.language)

    window.addEventListener('offline', this.offlineListener)
    window.addEventListener('online', this.onlineListener)
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
  destroyed() {
    window.removeEventListener('offline', this.offlineListener)
    window.removeEventListener('online', this.onlineListener)
  },
  methods: {
    offlineListener() {
      this.offline = true
    },
    onlineListener() {
      this.offline = false
    },
  },
}
</script>
<!-- these styles must be global -->
<!-- eslint-disable-next-line vue-scoped-css/enforce-style-type -->
<style lang="scss">
@import 'src/scss/tailwind';
@import 'src/scss/global';
@import '~@mdi/font/css/materialdesignicons.css';

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

.v-btn--open {
  background: #b0bec5 !important;
  color: rgba(0, 0, 0, 0.87) !important;
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

<style scoped>
/* <v-footer> is transformed to <footer class="v-footer"> */
/* eslint-disable-next-line vue-scoped-css/no-unused-selector */
.v-footer {
  border-top: 3px solid #c80d0d;
  z-index: 4;
  background: #fbdfdf;
  color: #7a0f0f;
  font-size: 80%;
}
</style>
