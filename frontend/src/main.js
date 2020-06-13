import Vue from 'vue'
import App from './App.vue'
import router from '@/router'
import { vuetify, auth, apiStore, filterLoading, formBaseComponents, ignoreNativeBindingWarnMessages, i18n, veeValidate } from '@/plugins'
import { store } from '@/plugins/store'

Vue.use(auth)
Vue.use(apiStore)
Vue.use(filterLoading)
Vue.use(formBaseComponents)
Vue.use(ignoreNativeBindingWarnMessages)
Vue.use(veeValidate)

new Vue({
  router,
  store,
  vuetify,
  i18n,
  render: h => h(App)
}).$mount('#app')
