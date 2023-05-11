const fs = require('fs')
const path = require('path')

function moveDownloads(config, destSubDir) {
  const downloadsFolder = config.downloadsFolder
  const destDirName = path.join(downloadsFolder, destSubDir)
  fs.mkdirSync(destDirName, { recursive: true })
  fs.readdir(downloadsFolder, { withFileTypes: true }, (err, files) => {
    if (err) {
      console.log(err)
    } else {
      files.forEach((file) => {
        if (file.isDirectory()) {
          console.log(`not moving directory ${path.join(downloadsFolder, file.name)}`)
          return
        }
        if (file.name !== '.gitkeep') {
          const oldPath = path.join(downloadsFolder, file.name)
          const newPath = path.join(destDirName, file.name)
          fs.rename(oldPath, newPath, () => {
            console.log(`moved file from ${oldPath} to ${newPath}`)
          })
        }
      })
    }
  })
  return null
}

module.exports = {
  moveDownloads
}
