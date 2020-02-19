import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
import VueAxios from 'vue-axios'
import normalize from 'hal-json-normalizer'
import { normalizeEntityUri } from '@/store/uriUtils'
import storeValueProxy from '@/store/storeValueProxy'

Vue.use(Vuex)
axios.defaults.withCredentials = true
Vue.use(VueAxios, axios)

export const API_ROOT = process.env.VUE_APP_ROOT_API

export const state = {
  api: {}
}

export const mutations = {
  /**
   * Adds a placeholder into the store that indicates that the entity with the given URI is currently being
   * fetched from the API and not yet available.
   * @param state Vuex state
   * @param uri   URI of the object that is being fetched
   */
  addEmpty (state, uri) {
    Vue.set(state.api, uri, { _meta: { self: uri, loading: true } })
  },
  /**
   * Adds entities loaded from the API to the Vuex store.
   * @param state Vuex state
   * @param data  An object mapping URIs to entities that should be merged into the Vuex state.
   */
  add (state, data) {
    Object.keys(data).forEach((uri) => {
      Vue.set(state.api, uri, data[uri])
    })
  },
  /**
   * Removes a single entity from the Vuex store.
   * @param state Vuex state
   * @param uri   URI of the entity to be removed
   */
  purge (state, uri) {
    Vue.delete(state.api, uri)
  },
  /**
   * Marks a single entity in the Vuex store as deleting, meaning the process of deletion is currently ongoing.
   * @param state Vuex state
   * @param uri   URI of the entity that is currently being deleted
   */
  deleting (state, uri) {
    Vue.set(state.api[uri]._meta, 'deleting', true)
  },
  /**
   * Marks a single entity in the Vuex store as normal again, after it has been marked as deleting before.
   * @param state Vuex state
   * @param uri   URI of the entity that failed to be deleted
   */
  deletingFailed (state, uri) {
    Vue.set(state.api[uri]._meta, 'deleting', false)
  }
}

const store = new Vuex.Store({
  state,
  mutations,
  strict: process.env.NODE_ENV !== 'production'
})
export default store

/**
 * Sends a POST request to the backend, in order to create a new entity.
 * @param uriOrCollection URI (or instance) of a collection in which the entity should be created
 * @param data            Payload to be sent in the POST request
 * @returns Promise       resolves when the POST request has completed and the entity is available
 *                        in the Vuex store.
 */
export const post = function (uriOrCollection, data) {
  const uri = normalizeEntityUri(uriOrCollection, API_ROOT)
  if (uri === null) {
    return Promise.reject(new Error(`Could not perform POST, "${uriOrCollection}" is not an entity or URI`))
  }
  return markAsDoneWhenResolved(axios.post(API_ROOT + uri, data).then(({ data }) => {
    // Workaround because API adds page parameter even to first page when it was not requested that way
    // TODO fix backend API and remove the next line
    data._links.self.href = uri
    storeHalJsonData(data)
    return get(uri)
  }))
}

/**
 * Reloads an entity from the API.
 *
 * @param uriOrEntity URI (or instance) of an entity to reload from the API
 * @returns entity    Entity from the store. Note that when fetching an object for the first time, a reactive
 *                    dummy is returned, which will be replaced with the true data through Vue's reactivity
 *                    system as soon as the API request finishes.
 */
export const reload = function (uriOrEntity) {
  return get(uriOrEntity, true)
}

/**
 * Retrieves an entity from the Vuex store, or from the API in case it is not already fetched or a reload
 * is forced.
 * This function attempts to hide all backend implementation details such as pagination, linked vs.
 * embedded relations and loading state and instead provide an easy-to-use and consistent interface for
 * developing frontend components.
 *
 * Basic usage in a Vue component:
 * computed: {
 *   allCamps () { return this.api.get('/camp').items }
 *   oneSpecificCamp () { return this.api.get(`/camp/${this.campId}`) }
 *   campUri () { return this.oneSpecificCamp._meta.self }
 *   eventTypes () { return this.oneSpecificCamp.event_types() }
 *   user () { return this.api.get().profile() } // Root endpoint ('/') and navigate through self-discovery API
 * },
 * created () {
 *   this.oneSpecificCamp._meta.loaded.then(() => {
 *     // do something now that the camp is loaded from the API
 *   })
 * }
 *
 * @param uriOrEntity URI (or instance) of an entity to load from the store or API
 * @param forceReload If true, the entity will be fetched from the API even if it is already in the Vuex store.
 *                    Note that the function will still return the old value in this case, but you can
 *                    wait for the new value using the ._meta.loaded promise.
 * @returns entity    Entity from the store. Note that when fetching an object for the first time, a reactive
 *                    dummy is returned, which will be replaced with the true data through Vue's reactivity
 *                    system as soon as the API request finishes.
 */
export const get = function (uriOrEntity, forceReload = false) {
  const forceReloadingEmbeddedCollection = forceReload && uriOrEntity._meta && uriOrEntity._meta.reload && uriOrEntity._meta.reload.uri
  const uri = forceReloadingEmbeddedCollection
    ? normalizeEntityUri(uriOrEntity._meta.reload.uri, API_ROOT)
    : normalizeEntityUri(uriOrEntity, API_ROOT)
  if (uri === null) {
    if (uriOrEntity[Symbol.for('isLoadingProxy')]) {
      // A loadingProxy is safe to return without breaking the UI.
      return uriOrEntity
    }
    // We don't know anything about the requested object, something is wrong.
    throw new Error(`Could not perform GET, "${uriOrEntity}" is not an entity or URI`)
  }

  const storeData = load(uri, forceReload)
  return forceReloadingEmbeddedCollection
    ? storeValueProxy(storeData)[uriOrEntity._meta.reload.property]()
    : storeValueProxy(storeData)
}

/**
 * Loads the entity specified by the URI from the Vuex store, or from the API if necessary. If applicable,
 * sets the loaded promise on the entity in the Vuex store.
 * @param uri         URI of the entity to load
 * @param forceReload If true, the entity will be fetched from the API even if it is already in the Vuex store.
 * @returns entity    the current entity data from the Vuex store. Note: This may be a reactive dummy if the
 *                    backend request is still ongoing.
 */
function load (uri, forceReload) {
  const existsInStore = (uri in store.state.api)
  const isLoading = existsInStore && (store.state.api[uri]._meta || {}).loading

  if (!existsInStore) {
    store.commit('addEmpty', uri)
  }
  if (isLoading) {
    // Reuse the loading entity and loaded promise that is already waiting for a pending API request
    return store.state.api[uri]
  }

  let dataFinishedLoading = Promise.resolve(store.state.api[uri])
  if (!existsInStore || forceReload) {
    dataFinishedLoading = loadFromApi(uri)
  } else if (store.state.api[uri]._meta.loaded) {
    // reuse the existing promise from the store if possible
    dataFinishedLoading = store.state.api[uri]._meta.loaded
  }

  // We mutate the store state here without telling Vuex about it, so it won't complain and won't make loaded reactive.
  // The promise is needed in the store for some special cases when a loading entity is requested a second time with
  // this.api.get(...) or this.api.reload(...).
  store.state.api[uri]._meta.loaded = markAsDoneWhenResolved(dataFinishedLoading)

  return store.state.api[uri]
}

/**
 * Loads the entity specified by the URI from the API and stores it into the Vuex store. Returns a promise
 * that resolves to the raw data stored in the Vuex store (needs to be wrapped into a storeValueProxy before
 * being usable in Vue components).
 * @param uri       URI of the entity to load from the API
 * @returns Promise resolves to the raw data stored in the Vuex store after the API request completes, or
 *                  rejects when the API request fails
 */
function loadFromApi (uri) {
  return new Promise((resolve, reject) => {
    axios.get(API_ROOT + uri).then(
      ({ data }) => {
        // Workaround because API adds page parameter even to first page when it was not requested that way
        // TODO fix backend API and remove the next line
        data._links.self.href = uri
        storeHalJsonData(data)
        resolve(store.state.api[uri])
      },
      ({ response }) => {
        if (response.status === 404) {
          return deleted(uri)
        }
        reject(response)
      }
    )
  })
}

/**
 * Loads the URI of a related entity from the store, or the API in case it is not already fetched.
 *
 * @param uriOrEntity URI (or instance) of an entity from the API
 * @param relation    the name of the relation for which the URI should be retrieved
 * @returns Promise   resolves to the URI of the related entity.
 */
export const href = async function (uriOrEntity, relation) {
  const self = normalizeEntityUri(await get(uriOrEntity)._meta.loaded, API_ROOT)
  const href = (state.api[self][relation] || {}).href
  return href ? API_ROOT + href : href
}

/**
 * Sends a PATCH request to the backend, in order to update some fields in an existing entity.
 * @param uriOrEntity URI (or instance) of an entity which should be updated
 * @param data        Payload (fields to be updated) to be sent in the PATCH request
 * @returns Promise   resolves when the PATCH request has completed and the updated entity is available
 *                    in the Vuex store.
 */
const patch = function (uriOrEntity, data) {
  const uri = normalizeEntityUri(uriOrEntity, API_ROOT)
  if (uri === null) {
    return Promise.reject(new Error(`Could not perform PATCH, "${uriOrEntity}" is not an entity or URI`))
  }
  return markAsDoneWhenResolved(axios.patch(API_ROOT + uri, data).then(({ data }) => {
    // Workaround because API adds page parameter even to first page when it was not requested that way
    // TODO fix backend API and remove the next line
    data._links.self.href = uri
    storeHalJsonData(data)
    return get(uri)
  }, ({ response }) => {
    if (response.status === 404) {
      return deleted(uri)
    }
  }))
}

/**
 * Removes a single entity from the Vuex store (but does not delete it using the API). Note that if the
 * entity is currently referenced and displayed through any other entity, the reactivity system will
 * immediately re-fetch the purged entity from the API in order to re-display it.
 * @param uriOrEntity URI (or instance) of an entity which should be removed from the Vuex store
 */
const purge = function (uriOrEntity) {
  const uri = normalizeEntityUri(uriOrEntity, API_ROOT)
  if (uri === null) {
    // Can't purge an unknown URI, do nothing
    return
  }
  store.commit('purge', uri)
  return uri
}

/**
 * Attempts to permanently delete a single entity using a DELETE request to the API.
 * This function performs the following operations when given the URI of an entity E:
 * 1. Marks E in the Vuex store with the ._meta.deleting flag
 * 2. Sends a DELETE request to the API in order to delete E from the backend (in case of failure, the
 *    deleted flag is reset and the operation is aborted)
 * 3. Finds all entities [...R] in the store that reference E (e.g. find the corresponding camp when
 *    deleting an event) and reloads them from the API
 * 4. Purges E from the Vuex store
 * @param uriOrEntity URI (or instance) of an entity which should be deleted
 * @returns Promise   resolves when the DELETE request has completed and either all related entites have
 *                    been reloaded from the API, or the failed deletion has been cleaned up.
 */
const del = function (uriOrEntity) {
  const uri = normalizeEntityUri(uriOrEntity, API_ROOT)
  if (uri === null) {
    // Can't delete an unknown URI, do nothing
    return Promise.reject(new Error(`Could not perform DELETE, "${uriOrEntity}" is not an entity or URI`))
  }
  store.commit('deleting', uri)
  return markAsDoneWhenResolved(axios.delete(API_ROOT + uri)
    .then(() => deleted(uri), ({ response }) => deletingFailed(uri, response)))
}

function valueIsArrayWithReferenceTo (value, uri) {
  return Array.isArray(value) && value.some(entry => valueIsReferenceTo(entry, uri))
}

function valueIsReferenceTo (value, uri) {
  const objectKeys = Object.keys(value)
  return objectKeys.length === 1 && objectKeys[0] === 'href' && value.href === uri
}

function findEntitiesReferencing (uri) {
  return Object.values(store.state.api)
    .filter((entity) => {
      return Object.values(entity).some(propertyValue =>
        valueIsReferenceTo(propertyValue, uri) || valueIsArrayWithReferenceTo(propertyValue, uri)
      )
    })
}

/**
 * Cleans up the Vuex store after an entity is found to be deleted (HTTP status 204 or 404) from the backend.
 * @param uri       URI of an entity which is not available (anymore) in the backend
 * @returns Promise resolves when the cleanup has completed and the Vuex store is up to date again
 */
function deleted (uri) {
  return Promise.all(findEntitiesReferencing(uri).map(outdatedEntity => reload(outdatedEntity)._meta.loaded))
    .then(() => purge(uri))
}

/**
 * Unsets the ._meta.deleted flag when an entity failed to be deleted from the backend.
 * @param uri      URI of an entity which failed to be deleted from backend
 * @param response HTTP response returned from the DELETE call to backend
 */
function deletingFailed (uri, response) {
  if (response.status !== 204) {
    store.commit('deletingFailed', uri)
  }
}

/**
 * Normalizes raw data from the backend and stores it into the Vuex store.
 * @param data HAL JSON data received from the backend
 */
function storeHalJsonData (data) {
  const normalizedData = normalize(data, {
    camelizeKeys: false,
    metaKey: '_meta',
    normalizeUri: (uri) => normalizeEntityUri(uri, API_ROOT),
    filterReferences: true
  })
  store.commit('add', normalizedData)
}

/**
 * Sets a flag on the given promise after completion, so that users of the promise can tell whether it is still
 * pending or not. This is needed so storeValueProxy can break infinite recursion.
 * @param promise   to be marked as done once it completes
 * @returns Promise the modified argument
 */
function markAsDoneWhenResolved (promise) {
  // empty catch is important so that our then handler runs in all cases
  promise.catch(() => {}).then(() => { promise[Symbol.for('done')] = true })
  return promise
}

/**
 * Define the API store methods available in all Vue components. The methods can be called as follows:
 *
 * // In a computed or method or lifecycle hook
 * let someEntity = this.api.get('/some/endpoint')
 * this.api.reload(someEntity)
 *
 * // In the <template> part of a Vue component
 * <li v-for="camp in api.get('/all/my/camps').items" key="camp._meta.self">...</li>
 */
Object.defineProperties(Vue.prototype, {
  api: {
    get () { return { post, get, reload, del, patch, purge, href } }
  }
})
