import * as Sentry from '@sentry/node'
import { CaptureConsole } from '@sentry/integrations'

export default defineNitroPlugin((nitroApp) => {
  const { sentry } = useRuntimeConfig()

  // If no sentry DSN set, ignore and warn in the console
  if (!sentry.dsn) {
    console.warn('Sentry DSN not set, skipping Sentry initialization')
    return
  }

  // Initialize Sentry
  Sentry.init({
    dsn: sentry.dsn,
    environment: sentry.environment,
    integrations: [new CaptureConsole({ levels: ['warn', 'error'] })],
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
