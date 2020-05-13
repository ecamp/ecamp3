import urltemplate from 'url-template'
import { API_ROOT, get } from '@/plugins/store'

function isEqualIgnoringOrder (array, other) {
  return array.length === other.length && array.every(elem => other.includes(elem))
}

/**
 * A templated link in the Vuex store looks like this: { href: '/some/uri{/something}', templated: true }
 * @param object         to be examined
 * @returns boolean      true if the object looks like a templated link, false otherwise
 */
function isTemplatedLink (object) {
  if (!object) return false
  return isEqualIgnoringOrder(Object.keys(object), ['href', 'templated']) && (object.templated === true)
}

/**
 * An entity reference in the Vuex store looks like this: { href: '/some/uri' }
 * @param object    to be examined
 * @returns boolean true if the object looks like an entity reference, false otherwise
 */
function isEntityReference (object) {
  if (!object) return false
  return isEqualIgnoringOrder(Object.keys(object), ['href'])
}

function containsLoadingEntityReference (array) {
  return array.some(entry => isEntityReference(entry) && get(entry.href)._meta.loading)
}

/**
 * A standalone collection in the Vuex store has an items property that is an array.
 * @param object    to be examined
 * @returns boolean true if the object looks like a standalone collection, false otherwise
 */
function isCollection (object) {
  return !!(object && Array.isArray(object.items))
}

/**
 * Creates a placeholder for an entity which has not yet finished loading from the API.
 * Such a loadingProxy can safely be used in Vue components, since it will render as an empty
 * string and Vue's reactivity system will replace it with the real data once that is available.
 *
 * Accessing nested functions in a loadingProxy yields another loadingProxy:
 * loadingProxy(...).campType().organization() // gives another loadingProxy
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
    get: function (target, prop, _) {
      if (prop === Symbol.for('isLoadingProxy')) {
        return true
      }
      if (prop === Symbol.toPrimitive) {
        return () => ''
      }
      if (['then', 'toJSON', Symbol.toStringTag, 'state', 'getters', '$options', '_isVue', '__file', 'render', 'constructor'].includes(prop)) {
        // This is necessary so that Vue's reactivity system understands to treat this loadingProxy
        // like a normal object.
        return undefined
      }
      if (prop === 'loading') {
        return true
      }
      if (prop === 'load') {
        return entityLoaded
      }
      if (prop === 'self') {
        return uri !== null ? API_ROOT + uri : uri
      }
      if (prop === '_meta') {
        // When _meta is requested on a loadingProxy, we keep on using the unmodified promise, because ._meta.load
        // is supposed to resolve to the whole object, not just the ._meta part of it
        return loadingProxy(entityLoaded, uri)
      }
      const propertyLoaded = entityLoaded.then(entity => entity[prop])
      if (prop === 'items') {
        return loadingArrayProxy(propertyLoaded)
      }
      // Normal property access: return a function that yields another loadingProxy and renders as empty string
      const result = templateParams => loadingProxy(propertyLoaded.then(property => property(templateParams)._meta.load))
      result.loading = true
      result.toString = () => ''
      return result
    }
  }
  return new Proxy({}, handler)
}

/**
 * Returns a placeholder for an array that has not yet finished loading from the API. The array placeholder
 * will respond to functional calls (like .find(), .map(), etc.) with further loading array proxies or
 * loading proxies. If passed the existingContent argument, random access and .length will also work.
 * @param arrayLoaded     Promise that resolves once the array has finished loading
 * @param existingContent optionally set the elements that are already known, for random access
 * @returns Array         a proxy that can act as a placeholder for an array that is still being loaded from the API
 */
function loadingArrayProxy (arrayLoaded, existingContent = []) {
  const singleResultFunctions = ['find']
  const arrayResultFunctions = ['map', 'flatMap', 'filter']
  singleResultFunctions.forEach(func => {
    existingContent[func] = (...args) => {
      const resultLoaded = arrayLoaded.then(array => array[func](...args))
      return loadingProxy(resultLoaded)
    }
  })
  arrayResultFunctions.forEach(func => {
    existingContent[func] = (...args) => {
      const resultLoaded = arrayLoaded.then(array => array[func](...args))
      return loadingArrayProxy(resultLoaded)
    }
  })
  return existingContent
}

/**
 * Given an array, replaces any entity references in the array with the entity loaded from the Vuex store
 * (or from the API if necessary), and returns that as a new array. In case some of the entity references in
 * the array have not finished loading yet, returns a loadingArrayProxy instead.
 * @param array   possibly mixed array of values and references
 * @returns array the new array with replaced items, or a loadingArrayProxy if any of the array elements
 *                is still loading.
 */
function mapArrayOfEntityReferences (array) {
  const arrayWithReplacedReferences = array.map(entry => {
    if (isEntityReference(entry)) {
      return get(entry.href)
    }
    return entry
  })
  if (containsLoadingEntityReference(array)) {
    const arrayCompletelyLoaded = Promise.all(array.map(entry => {
      if (isEntityReference(entry)) {
        return get(entry.href)._meta.load
      }
      return Promise.resolve(entry)
    }))
    return loadingArrayProxy(arrayCompletelyLoaded, arrayWithReplacedReferences)
  } else {
    return arrayWithReplacedReferences
  }
}

/**
 * Defines a property getter for the items property of a given target object.
 * The items property should always be a getter, in order to make the call to mapArrayOfEntityReferences
 * lazy, since that potentially fetches a large number of entities from the API.
 * @param target   object on which the items getter should be defined
 * @param items    array of items, which can be mixed primitive values and entity references
 * @returns object the target object with the added getter
 */
function addItemsGetter (target, items) {
  Object.defineProperty(target, 'items', { get: () => mapArrayOfEntityReferences(items) })
  return target
}

/**
 * Imitates a full standalone collection with an items property, even if there is no separate URI (as it
 * is the case with embedded collections).
 * Reloading an embedded collection requires special information. Since the embedded collection has no own
 * URI, we need to reload the whole entity containing the embedded collection. Some extra info about the
 * containing entity must therefore be passed to this function.
 * @param items          array of items, which can be mixed primitive values and entity references
 * @param reloadUri      URI of the entity containing the embedded collection (for reloading)
 * @param reloadProperty property in the containing entity under which the embedded collection is saved
 * @returns object the imitated collection object
 */
function embeddedCollectionProxy (items, reloadUri, reloadProperty) {
  const result = addItemsGetter({ _meta: { reload: { uri: reloadUri, property: reloadProperty } } }, items)
  result._meta.load = Promise.resolve(result)
  return result
}

/**
 * Takes data from the Vuex store and makes it more usable in frontend components. The data stored
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
 * let usable = storeValueProxy(storeData)
 * // Now we can use accessor methods
 * usable.reference_to_other_entity() // returns the result of this.api.get('/uri/of/other/entity')
 *
 * @param data                entity data from the Vuex store
 * @returns object            wrapped entity ready for use in a frontend component
 */
export default function storeValueProxy (data) {
  const meta = data._meta || { load: Promise.resolve() }

  if (meta.loading) {
    const entityLoaded = meta.load.then(loadedData => createStoreValueProxy(loadedData))
    return loadingProxy(entityLoaded, meta.self)
  }

  return createStoreValueProxy(data)
}

/**
 * Creates an actual storeValueProxy, by wrapping the given Vuex store data. The data must not be loading.
 * If the data has been loaded into the store before but is currently reloading, the old data will be
 * returned, along with a ._meta.load promise that resolves when the reload is complete.
 * @param data fully loaded entity data from the Vuex store
 */
function createStoreValueProxy (data) {
  const result = {}
  Object.keys(data).forEach(key => {
    const value = data[key]
    if (key === 'items' && isCollection(data)) {
      addItemsGetter(result, data[key])
    } else if (Array.isArray(value)) {
      result[key] = () => embeddedCollectionProxy(value, data._meta.self, key)
    } else if (isEntityReference(value)) {
      result[key] = () => get(value.href)
    } else if (isTemplatedLink(value)) {
      result[key] = templateParams => get(urltemplate.parse(value.href).expand(templateParams || {}))
    } else {
      result[key] = value
    }
  })

  // Use a trivial load promise to break endless recursion, except if we are currently reloading the data from the API
  const loadedPromise = data._meta.load && !data._meta.load[Symbol.for('done')]
    ? data._meta.load.then(reloadedData => storeValueProxy(reloadedData))
    : Promise.resolve(result)

  // Use a shallow clone of _meta, since we don't want to overwrite the ._meta.load promise or self link in the Vuex store
  result._meta = { ...data._meta, load: loadedPromise, self: API_ROOT + data._meta.self }
  return result
}
