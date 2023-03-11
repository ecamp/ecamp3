const fs = require('fs')
const path = require('path')

function deleteDownloads(config) {
  const dirPath = config.downloadsFolder
  fs.readdir(dirPath, (err, files) => {
    if (err) {
      console.log(err)
    } else {
      files.forEach((file) => {
        if (file !== '.gitkeep') {
          fs.unlink(path.join(dirPath, file), () => {
            console.log('Removed ' + file)
          })
        }
      })
    }
  })
  return null
}

module.exports = {
  deleteDownloads,
}
