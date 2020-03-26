import Vue from 'vue'
import App from './App.vue'
import router from '@/router'
import { Vuetify, Auth, Store, FilterLoading } from '@/plugins'

Vue.use(Auth)
Vue.use(Store)
Vue.use(FilterLoading)

new Vue({
  router,
  Store,
  Vuetify,
  render: h => h(App)
}).$mount('#app')
