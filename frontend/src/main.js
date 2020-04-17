import Vue from 'vue'
import App from './App.vue'
import router from '@/router'
import i18n from './i18n'
import { vuetify, auth, store, filterLoading, formBaseComponents, ignoreNativeBindingWarnMessages, veeValidate } from '@/plugins'

Vue.use(auth)
Vue.use(store)
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
