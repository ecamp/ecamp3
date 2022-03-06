const { Router } = require('express')

const router = Router()

// simple health check endpoint for Kubernetes
router.use('/health', (req, res) => {
  const data = {
    uptime: process.uptime(),
    message: 'Ok',
    date: new Date(),
  }

  res.status(200).send(data)
})

module.exports = router
