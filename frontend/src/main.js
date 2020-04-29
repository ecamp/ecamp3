import Vue from 'vue'
import App from './App.vue'
import router from '@/router'
import { vuetify, auth, store, filterLoading, formBaseComponents, ignoreNativeBindingWarnMessages, i18n } from '@/plugins'

Vue.use(auth)
Vue.use(store)
Vue.use(filterLoading)
Vue.use(formBaseComponents)
Vue.use(ignoreNativeBindingWarnMessages)

new Vue({
  router,
  store,
  vuetify,
  i18n,
  render: h => h(App)
}).$mount('#app')
