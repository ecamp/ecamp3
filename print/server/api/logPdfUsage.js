/**
 * Endpoint to log usage of clientPrint
 */

export default defineEventHandler(async (event) => {
  const queryParams = getQuery(event)
  const logOutput = {
    timestamp: new Date(),
    type: 'clientPrint',
  }
  try {
    logOutput.config = queryParams.config ? JSON.parse(queryParams.config) : null
    logOutput.status = 204
    return { status: 204 }
  } catch {
    logOutput.status = 400
    return { status: 400, message: 'Invalid config format' }
  } finally {
    console.log(JSON.stringify(logOutput))
  }
})
