import Vue from 'vue'
import App from './App.vue'
import router from '@/router'
import { vuetify, auth, apiStore, store, filterLoading, formBaseComponents, ignoreNativeBindingWarnMessages, i18n, veeValidate, vueMoment, moment } from '@/plugins'
import { TiptapVuetifyPlugin } from 'tiptap-vuetify'

Vue.use(auth)
Vue.use(apiStore)
Vue.use(filterLoading)
Vue.use(formBaseComponents)
Vue.use(ignoreNativeBindingWarnMessages)
Vue.use(veeValidate)
Vue.use(vueMoment, { moment })
Vue.use(TiptapVuetifyPlugin, { vuetify, iconsGroup: 'mdi' })

new Vue({
  router,
  store,
  vuetify,
  i18n,
  render: h => h(App)
}).$mount('#app')
