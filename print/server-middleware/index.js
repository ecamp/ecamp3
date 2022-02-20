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
const pdfGenerator = require('./routes/pdfGenerator.js')

app.use(cookieParser())

// Import API Routes
app.use(pdfGenerator)

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
