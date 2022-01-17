const express = require('express')
const cookieParser = require('cookie-parser')
const cors = require('cors')

// Create express instance
const app = express()

// allow cross origin access
const corsOptions = {
  origin: process.env.FRONTEND_URL,
  credentials: true,
}
app.use(cors(corsOptions))

// Require API routes
const test1 = require('./routes/test1.js')
const test2 = require('./routes/test2.js')
const test3 = require('./routes/test3.js')
const test4 = require('./routes/test4.js')

app.use(cookieParser())

// Import API Routes
app.use(test1)
app.use(test2)
app.use(test3)
app.use(test4)

// Export express app
module.exports = app

// Start standalone server if directly running
if (require.main === module) {
  const port = process.env.PORT || 3001
  app.listen(port, () => {
    // eslint-disable-next-line no-console
    console.log(`API server listening on port ${port}`)
  })
}
