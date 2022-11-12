// eslint-disable-next-line no-unused-vars
module.exports = (path, fs) => ({
  packageDirectory: function (filename) {
    return filename.substring(0, 4)
  },
})
