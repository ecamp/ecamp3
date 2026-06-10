/**
 * Endpoint to log usage of clientPrint
 */

export default defineEventHandler(async (event) => {
  let logOutput = {
    timestamp: new Date(),
    type: 'clientPrint',
  }
  try {
    const body = await readBody(event)
    logOutput = { ...body, ...logOutput }
    logOutput.status ||= 200
    return { status: 204 }
  } catch {
    logOutput.status = 400
    return { status: 400, message: 'Invalid format' }
  } finally {
    console.log(JSON.stringify(logOutput))
  }
})
