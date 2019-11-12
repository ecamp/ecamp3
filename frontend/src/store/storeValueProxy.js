function isLink (object) {
  if (!object) return false
  const objectKeys = Object.keys(object)
  return objectKeys.length === 1 && objectKeys[0] === 'href'
}

function isCollection (object) {
  return object && Array.isArray(object['items'])
}

export function loadingProxy (uri = null) {
  const handler = {
    get: function (target, prop, receiver) {
      if (prop === Symbol.toPrimitive) {
        return () => ''
      }
      if (prop === '_meta') {
        return loadingProxy(uri)
      }
      if (prop === 'loading') {
        return true
      }
      if (prop === 'self') {
        return uri
      }
      if (prop === 'items') {
        return []
      }
      let result = () => loadingProxy()
      result.toString = () => ''
      return result
    }
  }
  return new Proxy({}, handler)
}

function mapArrayOfLinks (vm, array) {
  return array.map(entry => {
    if (isLink(entry)) {
      return vm.api.get(entry.href)
    }
    return entry
  })
}

function collectionProxy (vm, array) {
  return {
    get items () {
      return mapArrayOfLinks(vm, array)
    }
  }
}

export default function storeValueProxy (vm, data) {
  const meta = data._meta || {}
  if (meta.loading) {
    return loadingProxy(meta.self)
  }
  const result = {}
  Object.keys(data).forEach(key => {
    if (key === 'items' && isCollection(data)) {
      // Define this as a getter to make it evaluate only lazily
      Object.defineProperty(result, key, { get: () => mapArrayOfLinks(vm, data[key]) })
      return
    }
    const value = data[key]
    if (Array.isArray(value)) {
      result[key] = () => collectionProxy(vm, value)
    } else if (isLink(value)) {
      result[key] = () => vm.api.get(value.href)
    } else {
      // No getter here because we already had to evaluate data[key] by now
      result[key] = value
    }
  })
  return result
}
