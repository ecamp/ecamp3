function isLink (object) {
  if (!object) return false
  const objectKeys = Object.keys(object)
  return objectKeys.length === 1 && objectKeys[0] === 'href'
}

function isCollection (object) {
  if (!object) return false
  // TODO is this check appropriate? Should we check for _page in _meta instead?
  return !object.hasOwnProperty('id') && object.hasOwnProperty('items') && Array.isArray(object.items)
}

function loadingProxy () {
  const handler = {
    get: function (target, prop, receiver) {
      // TODO can the loading flag maybe be removed once we use loadingProxies everywhere?
      if (prop === 'loading') {
        return true
      }
      return loadingProxy()
    },
    apply: function () {
      return loadingProxy()
    }
  }
  return new Proxy({}, handler)
}

function collectionProxy (vm, array) {
  return {
    get items () {
      return array.map(entry => {
        if (isLink(entry)) {
          return vm.api(entry.href)
        }
      })
    }
  }
}

export default function storeValueProxy (vm, data) {
  if ((data._meta || {}).loading) {
    return loadingProxy()
  }
  if (isCollection(data)) {
    // TODO implement paginated collections properly to hide the difference to embedded collections
    console.error('paginated collections not implemented')
  }
  const result = {}
  Object.keys(data).forEach(key => {
    const value = data[key]
    if (Array.isArray(value)) {
      result[key] = () => collectionProxy(vm, value)
    } else if (isLink(value)) {
      result[key] = () => vm.api(value.href)
    } else {
      Object.defineProperty(result, key, { get: () => data[key] })
    }
  })
  return result
}
