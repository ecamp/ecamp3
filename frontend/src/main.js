import Vue from 'vue'
import App from './App.vue'
import '@/auth'
import router from '@/router'
import BootstrapVue from 'bootstrap-vue'
import axios from 'axios'
import VueAxios from 'vue-axios'

Vue.use(BootstrapVue)
Vue.use(VueAxios, axios)

new Vue({
  router,
  render: h => h(App)
}).$mount('#app')
