import Vue from 'vue'
import App from './App.vue'
import '@/auth'
import router from '@/router'
import BootstrapVue from 'bootstrap-vue'
import store from './store'

Vue.use(BootstrapVue)

Vue.component('empty-layout', () => import('./layouts/EmptyLayout'))
Vue.component('default-layout', () => import('./layouts/DefaultLayout'))

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
