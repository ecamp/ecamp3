import Vue from 'vue'
import App from './App.vue'
import router from '@/router.js'
import {
  vuetifyLoader,
  auth,
  storeLoader,
  formBaseComponents,
  ignoreNativeBindingWarnMessages,
  i18n,
  dayjs,
  color,
  veeValidate,
} from './plugins'
import { store } from './plugins/store'
import { vuetify } from './plugins/vuetify'
import { getEnv } from '@/environment.js'
import * as Sentry from '@sentry/vue'
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'

import { ClickOutside, Resize } from 'vuetify/lib/directives'
import ResizeObserver from 'v-resize-observer'

const env = getEnv()
if (env && env.SENTRY_FRONTEND_DSN) {
  const sentryEnvironment = env.SENTRY_ENVIRONMENT ?? 'local'
  Sentry.init({
    Vue,
    dsn: env.SENTRY_FRONTEND_DSN,
    environment: sentryEnvironment,
    enableTracing: false,
    autoSessionTracking: false,
    logErrors: process.env.NODE_ENV !== 'production',
  })
}

Vue.use(auth)
Vue.use(formBaseComponents)
Vue.use(ignoreNativeBindingWarnMessages)
Vue.use(storeLoader)
Vue.use(vuetifyLoader)
Vue.use(dayjs)
Vue.use(color)
Vue.use(veeValidate)
Vue.use(Toast, {
  maxToasts: 2,
})

// manually importing necessary vuetify directives (there's no auomatic vuetify-loader for vitejs)
Vue.directive('click-outside', ClickOutside)
Vue.directive('resize', Resize)
Vue.directive('resizeobserver', ResizeObserver.directive)

new Vue({
  router,
  store,
  vuetify,
  i18n,
  render: (h) => h(App),
}).$mount('#app')
