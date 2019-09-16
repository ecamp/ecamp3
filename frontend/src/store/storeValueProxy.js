function isLink (object) {
  if (!object) return false
  const objectKeys = Object.keys(object)
  return objectKeys.length === 1 && objectKeys[0] === 'href'
}

function isCollection (object) {
  if (!object) return false
  // TODO is this check appropriate? Could we check for page in _meta instead? Can backend even send it in _meta?
  return !object.hasOwnProperty('id') && object.hasOwnProperty('items') && Array.isArray(object.items)
}

function loadingProxy () {
  const handler = {
    get: function (target, prop, receiver) {
      if (prop === Symbol.toPrimitive) {
        return () => ''
      }
      // TODO avoid adding a separate if here for each nestable primitive property...
      if (prop === '_meta') {
        return loadingProxy()
      }
      if (prop === 'loading') {
        return true
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

function collectionProxy (vm, array) {
  return {
    get items () {
      return array.map(entry => {
        if (isLink(entry)) {
          return vm.api(entry.href)
        }
        return entry
      })
    }
  }
}

function paginatedCollectionProxy (vm, data) {
  const allItems = []
  allItems.push(...data.items)
  if (isLink(data.next)) {
    const next = vm.api(data.next.href)
    if (!next._meta.loading) {
      allItems.push(...next.items)
    }
  }
  return collectionProxy(vm, allItems)
}

export default function storeValueProxy (vm, data) {
  if ((data._meta || {}).loading) {
    return loadingProxy()
  }
  const result = {}
  Object.keys(data).forEach(key => {
    if (key === 'items' && isCollection(data)) {
      Object.defineProperty(result, key, { get: () => paginatedCollectionProxy(vm, data).items, enumerable: true })
      // result[key] = paginatedCollectionProxy(vm, data).items
      return
    }
    const value = data[key]
    if (Array.isArray(value)) {
      result[key] = () => collectionProxy(vm, value)
    } else if (isLink(value)) {
      result[key] = () => vm.api(value.href)
    } else {
      // Object.defineProperty(result, key, { get: () => data[key], enumerable: true })
      result[key] = value
    }
  })
  return result
}
