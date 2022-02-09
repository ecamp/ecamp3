/**
 * Parses an error object returned by the API and returns as message string
 */
export default function (error) {
  let serverErrorMessage = null

  // 422 validation error
  if (error.name === 'ServerException' && error.response) {
    serverErrorMessage = error.response.data.detail
  } else {
    serverErrorMessage = error.message
  }

  return serverErrorMessage
}
