import Vue from 'vue'
import App from './App.vue'
import '@/auth'
import router from '@/router'
import BootstrapVue from 'bootstrap-vue'
import axios from 'axios'
import VueAxios from 'vue-axios'

Vue.use(BootstrapVue)
axios.defaults.withCredentials = true
Vue.use(VueAxios, axios)

Vue.component('empty-layout', () => import('./layouts/EmptyLayout'))
Vue.component('default-layout', () => import('./layouts/DefaultLayout'))

new Vue({
  router,
  render: h => h(App)
}).$mount('#app')
