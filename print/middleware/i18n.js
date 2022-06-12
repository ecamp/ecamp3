export default function ({
  isHMR,
  app,
  query,
  /* store,
  route,
  params,
  error,
  redirect, */
}) {
  const defaultLocale = app.i18n.fallbackLocale

  // If middleware is called from hot module replacement, ignore it
  if (isHMR) {
    return
  }

  // Get locale from config
  const config = JSON.parse(query.config || '{}')
  const locale = config.language || defaultLocale

  /*
  if (!store.state.locales.includes(locale)) {
    return error({ message: 'This page could not be found.', statusCode: 404 })
  } */

  // Set locale
  // store.commit('SET_LANG', locale)
  // app.i18n.locale = store.state.locale
  app.i18n.locale = locale

  // set locale of dayjs
  app.$date.locale(locale)

  // If route is /<defaultLocale>/... -> redirect to /...
  /*
  if (
    locale === defaultLocale &&
    route.fullPath.indexOf('/' + defaultLocale) === 0
  ) {
    const toReplace =
      '^/' +
      defaultLocale +
      (route.fullPath.indexOf('/' + defaultLocale + '/') === 0 ? '/' : '')
    const re = new RegExp(toReplace)
    return redirect(route.fullPath.replace(re, '/'))
  } */
}
