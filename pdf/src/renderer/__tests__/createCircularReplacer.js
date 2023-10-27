export function createCircularReplacer() {
  const ancestors = []
  return function (key, value) {
    if (typeof value !== 'object' || value === null) {
      return value
    }
    while (ancestors.length > 0 && ancestors.at(-1) !== this) {
      ancestors.pop()
    }
    if (ancestors.includes(value)) {
      return '[Circular]'
    }
    ancestors.push(value)
    return value
  }
}
