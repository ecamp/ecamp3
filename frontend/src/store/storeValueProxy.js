function isLink (object) {
  if (!object) return false
  const objectKeys = Object.keys(object)
  return objectKeys.length === 1 && objectKeys[0] === 'href'
}

function isCollection (object) {
  if (!object) return false
  return object.hasOwnProperty('page_count')
}

function loadingProxy () {
  const handler = {
    get: function (target, prop, receiver) {
      if (prop === Symbol.toPrimitive) {
        return () => ''
      }
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
          return vm.api.get(entry.href)
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
    const next = vm.api.get(data.next.href)
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
      // Define this as a getter to make it evaluate only lazily
      Object.defineProperty(result, key, { get: () => paginatedCollectionProxy(vm, data).items, enumerable: true })
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
