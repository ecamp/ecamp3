import browserUpdate from 'browser-update'
import { createApp } from 'vue'
import App from './App.vue'
import router from '@/router.js'
import {
  auth,
  color,
  dayjs,
  formBaseComponents,
  head,
  i18n,
  storeLoader,
  veeValidate,
  vuetifyLoader,
} from './plugins'
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'

import { ClickOutside, Resize } from 'vuetify/directives'
import ResizeObserver from 'v-resize-observer'
import * as Sentry from '@sentry/vue'
import '@/scss/global.scss'
import '@/scss/tailwind.scss'
import { initRefresh } from '@/plugins/auth.js'
import { getEnv } from '@/environment.js'
import { isChunkLoadError } from '@/helpers/chunkLoadError.js'
import { notifyNewVersionAvailable } from '@/helpers/newVersionAvailable.js'

browserUpdate({
  required: {
    c: 90,
    f: 90,
    e: 90,
    s: 14,
    o: 80,
  },
  insecure: true,
})

const app = createApp(App)

const env = getEnv()
if (env && env.SENTRY_FRONTEND_DSN) {
  const sentryEnvironment = env.SENTRY_ENVIRONMENT ?? 'local'
  Sentry.init({
    app,
    dsn: env.SENTRY_FRONTEND_DSN,
    environment: sentryEnvironment,
    enableTracing: false,
    autoSessionTracking: false,
    logErrors: process.env.NODE_ENV !== 'production',
    ignoreErrors: [/Can't find variable: __firefox__/, /window\.__firefox__/],
  })
}

const previousErrorHandler = app.config.errorHandler
app.config.errorHandler = (error, instance, info) => {
  if (isChunkLoadError(error)) {
    notifyNewVersionAvailable()
    return
  }
  if (previousErrorHandler) {
    previousErrorHandler(error, instance, info)
  } else {
    // Keep Vue's default behaviour of surfacing unexpected errors.
    console.error(error)
  }
}

window.addEventListener('unhandledrejection', (event) => {
  if (isChunkLoadError(event.reason)) {
    notifyNewVersionAvailable()
  }
})

app.use(auth)
app.use(head)
app.use(formBaseComponents)
// app.use(ignoreNativeBindingWarnMessages)
app.use(storeLoader)
app.use(vuetifyLoader)
app.use(dayjs)
app.use(color)
app.use(veeValidate)
app.use(Toast, {
  maxToasts: 2,
})
app.use(router)
app.use(i18n)

// manually importing necessary vuetify directives (there's no auomatic vuetify-loader for vitejs)
app.directive('click-outside', ClickOutside)
app.directive('resize', Resize)
app.directive('resizeobserver', ResizeObserver.directive)

app.mount('#app')

// noinspection JSIgnoredPromiseFromCall
initRefresh()

export default app
