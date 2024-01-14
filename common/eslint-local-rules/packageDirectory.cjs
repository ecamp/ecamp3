module.exports = (path, fs) => ({
  packageDirectory: function (filename) {
    const { root } = path.parse(filename)

    let directory = filename
    while (directory !== root) {
      directory = path.dirname(directory)

      try {
        if (fs.statSync(path.resolve(directory, 'package.json')).isFile()) {
          return directory
        }
      } catch {
        // Ignore, try going up to the next directory
      }
    }

    return ''
  },
})
