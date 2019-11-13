function isLink (object) {
  if (!object) return false
  const objectKeys = Object.keys(object)
  return objectKeys.length === 1 && objectKeys[0] === 'href'
}

function isCollection (object) {
  return object && Array.isArray(object['items'])
}

export function loadingProxy (dataLoaded, uri) {
  const handler = {
    get: function (target, prop, receiver) {
      if (prop === Symbol('isLoadingProxy')) {
        return true
      }
      if (prop === Symbol.toPrimitive) {
        return () => ''
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
      const nestedLoaded = dataLoaded.then(result => result[prop])
      const nestedProxy = loadingProxy(nestedLoaded)
      if (prop === '_meta') {
        return nestedProxy
      }
      let result = () => nestedProxy
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

function addItemsGetter (vm, target, items) {
  // Items should be a getter to make the map operation evaluate lazily
  Object.defineProperty(target, 'items', { get: () => mapArrayOfLinks(vm, items) })
  return target
}

function embeddedCollectionProxy (vm, array) {
  // Imitate a full collection with an items property, even though we don't have a URI for this collection
  const result = addItemsGetter(vm, { _meta: {} }, array)
  result._meta.loaded = Promise.resolve(result)
  return result
}

export default function storeValueProxy (vm, data, rawDataFinishedLoading) {
  const meta = data._meta || {}
  if (meta.loading) {
    meta.loaded = rawDataFinishedLoading.then(result => {
      // In here, result._meta.loading should never be true
      return storeValueProxy(vm, result, Promise.resolve(result))
    })
    return loadingProxy(meta.loaded, meta.self)
  }
  const result = {}
  Object.keys(data).forEach(key => {
    const value = data[key]
    if (key === 'items' && isCollection(data)) {
      addItemsGetter(vm, result, data[key])
    } else if (Array.isArray(value)) {
      result[key] = () => embeddedCollectionProxy(vm, value)
    } else if (isLink(value)) {
      result[key] = () => vm.api.get(value.href)
    } else {
      result[key] = value
    }
  })
  result._meta.loaded = Promise.resolve(result)
  return result
}
