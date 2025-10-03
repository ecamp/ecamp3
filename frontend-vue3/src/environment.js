export function getEnv() {
  if (window.environment) {
    return window.environment
  }
  // you can overwrite the env variables locally in frontend/env.local
  // @see https://vitejs.dev/guide/env-and-mode.html
  const env = import.meta.env
  return {
    API_ROOT_URL: env.VITE_API_ROOT_URL ?? '/api',
    COOKIE_PREFIX: env.VITE_COOKIE_PREFIX ?? 'localhost_',
    PRINT_URL: env.VITE_PRINT_URL ?? '/print',
    SENTRY_FRONTEND_DSN: env.VITE_SENTRY_FRONTEND_DSN,
    SENTRY_ENVIRONMENT: env.VITE_SENTRY_ENVIRONMENT ?? 'local',
    DEPLOYMENT_TIME: env.VITE_DEPLOYMENT_TIME ?? '',
    VERSION: env.VITE_VERSION ?? '',
    VERSION_LINK_TEMPLATE:
      env.VITE_VERSION_LINK_TEMPLATE ??
      'https://github.com/ecamp/ecamp3/commit/{version}',
    TERMS_OF_SERVICE_LINK_TEMPLATE:
      env.TERMS_OF_SERVICE_LINK_TEMPLATE ?? 'https://ecamp3.ch/{lang}/tos',
    NEWS_LINK: env.NEWS_LINK ?? 'https://ecamp3.ch/blog',
    HELP_LINK: env.HELP_LINK ?? 'https://ecamp3.ch/faq',
    FEATURE_DEVELOPER: (env.VITE_FEATURE_DEVELOPER ?? 'true') === 'true',
    FEATURE_CHECKLIST: (env.VITE_FEATURE_CHECKLIST ?? 'true') === 'true',
    LOGIN_INFO_TEXT_KEY: env.VITE_LOGIN_INFO_TEXT_KEY ?? 'dev',
  }
}
