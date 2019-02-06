import Vue from 'vue'
import App from './App.vue'
import '@/auth'
import router from '@/router'
import BootstrapVue from 'bootstrap-vue'
import axios from 'axios'
import VueAxios from 'vue-axios'
import store from './store'

Vue.use(BootstrapVue)
axios.defaults.withCredentials = true
Vue.use(VueAxios, axios)

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
