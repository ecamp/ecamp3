import Vue from 'vue'
import App from './App.vue'
import router from '@/router'
import { vuetify, auth, store, filterLoading, formBaseComponents, i18n } from '@/plugins'

Vue.use(auth)
Vue.use(store)
Vue.use(filterLoading)
Vue.use(formBaseComponents)

new Vue({
  router,
  store,
  vuetify,
  i18n,
  render: h => h(App)
}).$mount('#app')
