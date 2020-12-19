import Vue from 'vue'
import App from './App.vue'
import router from '@/router'
import { vuetifyLoader, auth, storeLoader, filterLoading, formBaseComponents, ignoreNativeBindingWarnMessages, i18n, veeValidate, vueMoment, moment } from './plugins'
import { store } from './plugins/store'
import { vuetify } from './plugins/vuetify'

Vue.use(auth)
Vue.use(filterLoading)
Vue.use(formBaseComponents)
Vue.use(ignoreNativeBindingWarnMessages)
Vue.use(veeValidate)
Vue.use(vueMoment, { moment })
Vue.use(storeLoader)
Vue.use(vuetifyLoader)

new Vue({
  router,
  store,
  vuetify,
  i18n,
  render: h => h(App)
}).$mount('#app')
