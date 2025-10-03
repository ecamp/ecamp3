import { createApp } from 'vue'
import App from './App.vue'
import router from '@/router.js'
import {
  auth,
  color,
  dayjs,
  formBaseComponents,
  i18n,
  storeLoader,
  veeValidate,
  vuetifyLoader,
} from './plugins'
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'

import { ClickOutside, Resize } from 'vuetify/directives'
import ResizeObserver from 'v-resize-observer'

import '@/scss/main.scss'

const app = createApp(App)

// const env = getEnv()
// if (env && env.SENTRY_FRONTEND_DSN) {
//   const sentryEnvironment = env.SENTRY_ENVIRONMENT ?? 'local'
//   Sentry.init({
//     Vue,
//     dsn: env.SENTRY_FRONTEND_DSN,
//     environment: sentryEnvironment,
//     enableTracing: false,
//     autoSessionTracking: false,
//     logErrors: process.env.NODE_ENV !== 'production',
//   })
// }

app.use(auth)
app.use(formBaseComponents)
// app.use(ignoreNativeBindingWarnMessages)
app.use(storeLoader)
app.use(vuetifyLoader)
app.use(dayjs)
app.use(color)
//app.use(veeValidate)
app.use(Toast, {
  maxToasts: 2,
})
app.use(router)
app.use(i18n)

// manually importing necessary vuetify directives (there's no auomatic vuetify-loader for vitejs)
app.directive('click-outside', ClickOutside)
app.directive('resize', Resize)
app.directive('resizeobserver', ResizeObserver.directive)

// new Vue({
//   router,
//   store,
//   vuetify,
//   i18n,
//   render: (h) => h(App),
// }).$mount('#app')'

app.mount('#app')

export default app
