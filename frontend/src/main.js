import Vue from 'vue'
import App from './App.vue'
import router from '@/router.js'
import {
  vuetifyLoader,
  auth,
  storeLoader,
  filterLoading,
  formBaseComponents,
  ignoreNativeBindingWarnMessages,
  i18n,
  veeValidate,
  dayjs,
} from './plugins'
import { store } from './plugins/store'
import { vuetify } from './plugins/vuetify'
import VueCompositionAPI from '@vue/composition-api'
import * as Sentry from '@sentry/vue'
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'

import { Resize } from 'vuetify/lib/directives'

if (window.environment && window.environment.SENTRY_FRONTEND_DSN) {
  const environment = window.environment.SENTRY_ENVIRONMENT ?? 'http://localhost:3000'
  Sentry.init({
    Vue,
    dsn: window.environment.SENTRY_FRONTEND_DSN,
    environment,
    tracing: false,
    logErrors: process.env.NODE_ENV !== 'production',
  })
}

Vue.use(auth)
Vue.use(filterLoading)
Vue.use(formBaseComponents)
Vue.use(ignoreNativeBindingWarnMessages)
Vue.use(veeValidate)
Vue.use(storeLoader)
Vue.use(vuetifyLoader)
Vue.use(dayjs)
Vue.use(Toast, {
  maxToasts: 2,
})
Vue.use(VueCompositionAPI)

// manually importing necessary vuetify directives (there's no auomatic vuetify-loader for vitejs)
Vue.directive('resize', Resize)

new Vue({
  router,
  store,
  vuetify,
  i18n,
  render: (h) => h(App),
}).$mount('#app')
