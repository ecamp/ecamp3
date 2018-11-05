import Vue from 'vue'
import App from './App.vue'
import router from './router'
import BootstrapVue from 'bootstrap-vue'

Vue.use(BootstrapVue)

new Vue({
  router,
  render: h => h(App)
}).$mount('#app')
