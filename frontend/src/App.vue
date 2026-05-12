<template>
  <v-app :style="{ '--footer-height': footerHeight }">
    <router-view name="navigation" />

    <router-view name="aside" />

    <!-- main content -->
    <v-main>
      <router-view />
    </v-main>

    <FooterSharedCamp ref="footerSharedCamp" />

    <v-footer v-if="offline" app class="ec-footer offline">
      <p class="mb-0">
        <strong>{{ $t('global.info.offline.title') }}</strong>
        {{ $t('global.info.offline.description') }}
      </p>
    </v-footer>
    <v-snackbar-queue v-model="snackbarMessages"></v-snackbar-queue>
  </v-app>
</template>

<script>
import VueI18n from '@/plugins/i18n'
import { headEnvironment } from '@/plugins/index.js'
import { mapGetters } from 'vuex'
import { useHead } from '@unhead/vue'

export default {
  name: 'App',
  setup() {
    useHead({
      title: null,
      templateParams: {
        site: 'eCamp v3',
        separator: '·',
        environment: headEnvironment,
        section: null,
      },
      titleTemplate: '%environment %s %separator %section %separator %site',
    })
  },
  data: () => ({
    offline: false,
    footerHeight: '0px',
    mutationObserver: null,
  }),
  computed: {
    ...mapGetters(['snackbarMessages']),
  },
  watch: {
    offline() {
      // Use a small delay to ensure DOM has been updated
      setTimeout(() => this.updateFooterHeight(), 50)
    },
  },
  created() {
    this.$store.commit('setLanguage', this.$store.state.lang.language)

    window.addEventListener('offline', this.offlineListener)
    window.addEventListener('online', this.onlineListener)
    window.addEventListener('visibilitychange', this.visibilityChangeListener)
  },
  async mounted() {
    if (this.$auth.isLoggedIn()) {
      const user = await this.$auth.loadUser()
      const profile = await user.profile()._meta.load

      if (VueI18n.global.availableLocales.includes(profile.language)) {
        this.$store.commit('setLanguage', profile.language)
      }
    }

    // Wait for next tick to ensure all footer components are rendered
    this.$nextTick().then(() => {
      this.updateFooterHeight()
      // Set up MutationObserver to track footer visibility changes
      this.setupFooterObserver()
    })
  },
  unmounted() {
    window.removeEventListener('offline', this.offlineListener)
    window.removeEventListener('online', this.onlineListener)
    this.mutationObserver?.disconnect()
  },
  methods: {
    setupFooterObserver() {
      const appElement = this.$el
      if (!appElement) return

      this.mutationObserver = new MutationObserver(() => {
        this.updateFooterHeight()
      })

      this.mutationObserver.observe(appElement, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['style', 'class'],
      })
    },
    updateFooterHeight() {
      const footers = document.querySelectorAll('footer.v-footer')
      const borderWidth = parseInt(
        getComputedStyle(document.documentElement).getPropertyValue(
          '--footer-border-width'
        )
      )
      let totalHeight = 0
      footers.forEach((footer, idx) => {
        if (footer.offsetHeight > 0) {
          // v-footer tracks clientHeight which does not include borders (https://github.com/vuetifyjs/vuetify/blob/bf53f9e1021a4e7c9275cc21f1985ae754fb0635/packages/vuetify/src/components/VFooter/VFooter.tsx#L54C44-L54C56),
          // so if there is more than one footer (shared camp + offline) we need to subtract the border width,
          // because the border of the lower footer overlaps into the upper footer
          totalHeight += footer.offsetHeight + (idx > 0 ? -borderWidth : 0)
        }
      })
      this.footerHeight = totalHeight > 0 ? `${totalHeight}px` : '0px'
    },
    offlineListener() {
      this.offline = true
    },
    async onlineListener() {
      this.offline = false
      await this.$auth.initRefresh()
    },
    async visibilityChangeListener() {
      if (document.visibilityState !== 'visible') {
        return
      }
      await this.$auth.initRefresh()
    },
  },
}
</script>
<!-- these styles must be global -->
<!-- eslint-disable-next-line vue-scoped-css/enforce-style-type -->
<style lang="scss">
@use 'sass:map';
@use 'vuetify/settings';
//@import 'src/scss/tailwind';
//@import 'src/scss/global';
@import '~@mdi/font/css/materialdesignicons.css';

:root {
  --footer-border-width: 3px;
}

@media #{map.get(settings.$display-breakpoints, 'xs')} {
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

@media #{map.get(settings.$display-breakpoints, 'xs')} {
  .v-main > .v-container--fluid {
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

@media #{map.get(settings.$display-breakpoints, 'sm-and-down')} {
  // TODO: this changes look & feel of all v-containers. Do we really want this?
  .v-container.v-container--fluid {
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

// Shared styles for info footers (offline, shared camp)
.v-footer.ec-footer {
  border-top: var(--footer-border-width) solid;
  z-index: 4;
  font-size: 80%;
}
</style>

<style scoped>
/* <v-footer> is transformed to <footer class="v-footer"> */
/* eslint-disable-next-line vue-scoped-css/no-unused-selector */
.v-footer.offline {
  border-color: #c80d0d;
  background: #fbdfdf;
  color: #7a0f0f;
}
</style>
