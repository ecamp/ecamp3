function isLink (object) {
  if (!object) return false
  const objectKeys = Object.keys(object)
  return objectKeys.length === 1 && objectKeys[0] === 'href'
}

function loadingProxy () {
  const handler = {
    get: function (target, prop, receiver) {
      return loadingProxy()
    }
  }
  let result = () => new Proxy({ _meta: { loading: true } }, handler)
  result.toString = () => ''
  return result
}

export default function storeValueProxy (vm, data) {
  const handler = {
    get: function (target, prop, receiver) {
      if (!target.hasOwnProperty(prop)) {
        if (target._meta.loading) {
          return loadingProxy()
        }
        return Reflect.get(...arguments)
      }
      const value = Reflect.get(...arguments)
      if (Array.isArray(value)) {
        // TODO implement collections properly to hide the difference between embedded and paginated collection
        return () => value.map(entry => {
          if (isLink(entry)) {
            return vm.api(entry.href)
          }
        })
      }
      if (isLink(value)) {
        return () => vm.api(value.href)
      }
      return value
    }
  }
  return new Proxy(data, handler)
}
