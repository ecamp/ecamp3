const fs = require('fs')
const path = require('path')

function deleteDownloads(config) {
  const dirPath = config.downloadsFolder
  fs.readdir(dirPath, { withFileTypes: true }, (err, files) => {
    if (err) {
      console.log(err)
    } else {
      files.forEach((file) => {
        if (file.name === '.gitkeep') {
          return
        }
        if (file.isDirectory()) {
          console.log(`not deleting directory ${path.join(dirPath, file.name)}`)
          return
        }
        fs.unlink(path.join(dirPath, file.name), () => {
          console.log('Removed ' + file)
        })
      })
    }
  })
  return null
}

module.exports = {
  deleteDownloads,
}
