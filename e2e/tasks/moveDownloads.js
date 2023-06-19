const fs = require('fs')
const path = require('path')

function moveDownloads(config, destSubDir) {
  const downloadsFolder = config.downloadsFolder
  const destDirName = path.join(downloadsFolder, destSubDir)
  fs.mkdirSync(destDirName, { recursive: true })
  const files = fs.readdirSync(downloadsFolder, { withFileTypes: true })
  files.forEach((file) => {
    if (file.isDirectory()) {
      console.log(`not moving directory ${path.join(downloadsFolder, file.name)}`)
      return
    }
    if (file.name !== '.gitkeep') {
      const oldPath = path.join(downloadsFolder, file.name)
      const newPath = path.join(destDirName, file.name)
      fs.renameSync(oldPath, newPath)
      console.log(`moved file from ${oldPath} to ${newPath}`)
    }
  })
  return null
}

module.exports = {
  moveDownloads,
}
