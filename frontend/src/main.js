import Vue from 'vue'
import App from './App.vue'
import router from '@/router.js'
import { vuetifyLoader, auth, storeLoader, filterLoading, formBaseComponents, ignoreNativeBindingWarnMessages, i18n, veeValidate, dayjs } from './plugins'
import { store } from './plugins/store'
import { vuetify } from './plugins/vuetify'
import * as Sentry from '@sentry/vue'

if (window.environment && window.environment.SENTRY_FRONTEND_DSN) {
  Sentry.init({
    Vue,
    dsn: window.environment.SENTRY_FRONTEND_DSN,
    tracing: false,
    logErrors: process.env.NODE_ENV !== 'production'
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

new Vue({
  router,
  store,
  vuetify,
  i18n,
  render: h => h(App)
}).$mount('#app')
