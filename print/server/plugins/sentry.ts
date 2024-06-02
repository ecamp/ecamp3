import * as Sentry from '@sentry/node'

export default defineNitroPlugin((nitroApp) => {
  const { sentry } = useRuntimeConfig()

  // If no sentry DSN set, ignore and warn in the console
  if (!sentry.dsn) {
    console.warn('Sentry DSN not set, skipping Sentry initialization')
    return
  }

  // Next line can be disabled, once https://github.com/getsentry/sentry-javascript/issues/12059 is resolved
  globalThis._sentryEsmLoaderHookRegistered = true

  // Initialize Sentry
  Sentry.init({
    dsn: sentry.dsn,
    environment: sentry.environment,
    enableTracing: false,
    autoSessionTracking: false,
    integrations: [Sentry.captureConsoleIntegration({ levels: ['warn', 'error'] })],
  })

  nitroApp.hooks.hook('error', (error) => {
    Sentry.captureException(error)
  })

  nitroApp.hooks.hook('request', (event) => {
    event.context.$sentry = Sentry
  })

  nitroApp.hooks.hookOnce('close', async () => {
    await Sentry.close(2000)
  })
})
