export default defineEventHandler(async (event) => {
  setResponseStatus(event, 200)

  return {
    uptime: process.uptime(),
    message: 'Ok',
    date: new Date(),
  }
})
