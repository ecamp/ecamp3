/**
 * Parses an error object returned by hal-json-vuex and returns as message string
 */
import fallbackLocalesFor from '@/plugins/i18n/apiFallbackLocalesFor'

const serverErrorToString = (error) => {
  // API error
  // error.response is in API Problem Details format: https://www.rfc-editor.org/rfc/rfc7807
  if (error.name === 'ServerException' && error.response) {
    if (
      error.response?.headers?.['content-type']?.startsWith('application/problem+json')
    ) {
      return error.response.data.detail
    }

    return error.response?.data?.message || error.toString()
  }

  // other error thrown directly by Javascript (e.g. connection error)
  return error.message
}

function getApiTranslation(locale, violation) {
  if (!locale) {
    return null
  }
  const matchingTranslation = [locale, ...fallbackLocalesFor(locale)]
    .map((locale) => violation?.i18n?.translations?.[locale])
    .find((value) => value !== undefined)
  return matchingTranslation ?? null
}

/**
 * Parses an error object returned by hal-json-vuex and returns an object of propertypath => violationMessages[]
 * i18n is nullable if someone wants to use it without initialized localisation
 */
const transformViolations = (error, i18n = null) => {
  const serverErrorMessages = {}

  if (error.name === 'ServerException' && error.response?.status !== 422) {
    serverErrorMessages[0] = serverErrorToString(error)
    return serverErrorMessages
  }

  const violations = error.response?.data?.violations ?? []

  for (const i in violations) {
    const violation = violations[i]
    const propertyPath = violation.propertyPath
    if (!serverErrorMessages[propertyPath]) {
      serverErrorMessages[propertyPath] = []
    }
    const i18nKey = `api.${violation.i18n?.key}`
    const locale = i18n?.locale?.replaceAll('-', '_')
    const apiTranslation = getApiTranslation(locale, violation)

    if (i18n?.te(i18nKey)) {
      const parameters = violation.i18n?.parameters ?? {}
      serverErrorMessages[propertyPath].push(i18n.tc(i18nKey, parameters))
    } else if (apiTranslation) {
      serverErrorMessages[propertyPath].push(apiTranslation)
    } else {
      serverErrorMessages[propertyPath].push(violation.message)
    }
  }
  return serverErrorMessages
}

export { serverErrorToString, transformViolations }
