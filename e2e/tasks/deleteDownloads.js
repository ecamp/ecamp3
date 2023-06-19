const fs = require('fs')
const path = require('path')

function deleteDownloads(config) {
  const dirPath = config.downloadsFolder
  !fs.existsSync(dirPath) && fs.mkdirSync(dirPath, { recursive: true })
  const files = fs.readdirSync(dirPath, { withFileTypes: true })
  files.forEach((file) => {
    if (file.name === '.gitkeep') {
      return
    }
    if (file.isDirectory()) {
      console.log(`not deleting directory ${path.join(dirPath, file.name)}`)
      return
    }
    fs.unlinkSync(path.join(dirPath, file.name))
    console.log('Removed ' + file.name)
  })
  return null
}

module.exports = {
  deleteDownloads,
}
