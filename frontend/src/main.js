import Vue from 'vue'
import App from './App.vue'
import '@/auth'
import router from '@/router'
import store from './store'
import vuetify from './plugins/vuetify'

Vue.component('empty-layout', () => import('./layouts/EmptyLayout'))
Vue.component('default-layout', () => import('./layouts/DefaultLayout'))
Vue.component('camp-layout', () => import('./layouts/CampLayout'))
Vue.filter('loading', function (value, loadingState) {
  if (typeof value === 'function' && value().loading) {
    return loadingState
  } else {
    return value
  }
})

new Vue({
  router,
  store,
  vuetify,
  render: h => h(App)
}).$mount('#app')
