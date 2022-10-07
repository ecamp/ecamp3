/**
 * Parses an error object returned by hal-json-vuex and returns as message string
 */
const serverErrorToString = (error) => {
  // API error
  // error.response is in API Problem Details format: https://www.rfc-editor.org/rfc/rfc7807
  if (error.name === 'ServerException' && error.response) {
    if (error.response?.headers['content-type'].startsWith('application/problem+json')) {
      return error.response.data.detail
    }

    return error.response?.data?.message || error.toString()
  }

  // other error thrown directly by Javascript (e.g. connection error)
  return error.message
}

/**
 * Parses an error object returned by hal-json-vuex and returns as message array
 */
const serverErrorToArray = (error, fieldname) => {
  let serverErrorMessages = []

  if (error.name === 'ServerException' && error.response?.status !== 422) {
    serverErrorMessages[0] = serverErrorToString(error)
    return serverErrorMessages
  }

  // Validation Error(422): build message array from violations array
  error.response.data.violations.forEach((violation) => {
    if (violation.propertyPath === fieldname) {
      serverErrorMessages.push(`${violation.message}`)
    } else {
      serverErrorMessages.push(`${violation.propertyPath}: ${violation.message}`)
    }
  })

  return serverErrorMessages
}

export { serverErrorToString, serverErrorToArray }
