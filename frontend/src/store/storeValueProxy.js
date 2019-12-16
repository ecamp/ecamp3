/**
 * An entity reference in the Vuex store looks like this: { href: '/some/uri' }
 * @param object    to be examined
 * @returns boolean true if the object looks like an entity reference, false otherwise
 */
function isEntityReference (object) {
  if (!object) return false
  const objectKeys = Object.keys(object)
  return objectKeys.length === 1 && objectKeys[0] === 'href'
}

/**
 * A standalone collection in the Vuex store has an items property that is an array.
 * @param object    to be examined
 * @returns boolean true if the object looks like a standalone collection, false otherwise
 */
function isCollection (object) {
  return !!(object && Array.isArray(object['items']))
}

/**
 * Creates a placeholder for an entity which has not yet finished loading from the API.
 * Such a loadingProxy can safely be used in Vue components, since it will render as an empty
 * string and Vue's reactivity system will replace it with the real data once that is available.
 *
 * Accessing nested functions in a loadingProxy yields another loadingProxy:
 * loadingProxy(...).camp_type().organization() // gives another loadingProxy
 *
 * Using a loadingProxy or a property of a loadingProxy in a view renders to empty strings:
 * let user = loadingProxy(...)
 * 'The "' + user + '" is called "' + user.name + '"' // gives 'The "" is called ""'
 *
 * @param entityLoaded a Promise that resolves to a storeValueProxy when the entity has finished
 *                     loading from the API
 * @param uri          optional URI of the entity being loaded, if available. If passed, the
 *                     returned loadingProxy will return it in calls to .self and ._meta.self
 * @returns object     a loadingProxy
 */
function loadingProxy (entityLoaded, uri = null) {
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
      const nestedLoaded = entityLoaded.then(entity => entity[prop])
      if (prop === '_meta') {
        return loadingProxy(nestedLoaded, uri)
      }
      // Normal property access: return a function that yields another loadingProxy and renders as empty string
      let result = () => loadingProxy(nestedLoaded)
      result.toString = () => ''
      return result
    }
  }
  return new Proxy({}, handler)
}

/**
 * Given an array, replaces any entity references in the array with the entity loaded from the Vuex store
 * (or from the API if necessary), and returns that as a new array.
 * @param vm      Vue instance
 * @param array   possibly mixed array of values and references
 * @returns array the new array with replaced items
 */
function mapArrayOfEntityReferences (vm, array) {
  return array.map(entry => {
    if (isEntityReference(entry)) {
      return vm.api.get(entry.href)
    }
    return entry
  })
}

/**
 * Defines a property getter for the items property of a given target object.
 * The items property should always be a getter, in order to make the call to mapArrayOfEntityReferences
 * lazy, since that potentially fetches a large number of entities from the API.
 * @param vm       Vue instance
 * @param target   object on which the items getter should be defined
 * @param items    array of items, which can be mixed primitive values and entity references
 * @returns object the target object with the added getter
 */
function addItemsGetter (vm, target, items) {
  Object.defineProperty(target, 'items', { get: () => mapArrayOfEntityReferences(vm, items) })
  return target
}

/**
 * Imitates a full standalone collection with an items property, even if there is no separate URI (as it
 * is the case with embedded collections).
 * @param vm       Vue instance
 * @param items    array of items, which can be mixed primitive values and entity references
 * @returns object the imitated collection object
 */
function embeddedCollectionProxy (vm, items) {
  const result = addItemsGetter(vm, { _meta: {} }, items)
  result._meta.loaded = Promise.resolve(result)
  return result
}

/**
 * Takes data from the Vuex store and makes it more useable in frontend components. The data stored
 * in the Vuex store should always be JSON serializable according to
 * https://github.com/vuejs/vuex/issues/757#issuecomment-297668640. Therefore, we wrap the data into
 * a new object, and provide accessor methods for related entities. Such an accessor method fetches the
 * related entity from the Vuex store (or the API if necessary) when called. In case the related entity
 * is still being loaded from the API, a loadingProxy is returned.
 *
 * Example:
 * // Data of an entity like it comes from the Vuex store:
 * let storeData = {
 *   numeric_property: 3,
 *   reference_to_other_entity: {
 *     href: '/uri/of/other/entity'
 *   },
 *   _meta: {
 *     self: '/self/uri'
 *   }
 * }
 * // Apply storeValueProxy
 * let usable = storeValueProxy(vm, storeData)
 * // Now we can use accessor methods
 * usable.reference_to_other_entity() // returns the result of this.api.get('/uri/of/other/entity')
 *
 * @param vm                  Vue instance
 * @param data                entity data from the Vuex store
 * @returns object            wrapped entity ready for use in a frontend component
 */
export default function storeValueProxy (vm, data) {
  const meta = data._meta || {}

  if (meta.loading) {
    let entityLoaded = meta.loaded.then(loadedData => createStoreValueProxy(vm, loadedData))
    return loadingProxy(entityLoaded, meta.self)
  }

  return createStoreValueProxy(vm, data)
}

/**
 * Creates an actual storeValueProxy, by wrapping the given Vuex store data. The data must not be loading.
 * @param vm   Vue instance
 * @param data fully loaded entity data from the Vuex store
 */
function createStoreValueProxy (vm, data) {
  const result = {}
  Object.keys(data).forEach(key => {
    const value = data[key]
    if (key === 'items' && isCollection(data)) {
      addItemsGetter(vm, result, data[key])
    } else if (Array.isArray(value)) {
      result[key] = () => embeddedCollectionProxy(vm, value)
    } else if (isEntityReference(value)) {
      result[key] = () => vm.api.get(value.href)
    } else {
      result[key] = value
    }
  })
  // Use a shallow clone of _meta, since we don't want to overwrite the ._meta.loaded promise in the Vuex store
  result._meta = { ...data._meta, loaded: Promise.resolve(result) }
  return result
}
