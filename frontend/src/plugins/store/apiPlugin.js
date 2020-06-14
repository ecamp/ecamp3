import Vue from 'vue'
import axios from 'axios'
import VueAxios from 'vue-axios'
import normalize from 'hal-json-normalizer'
import urltemplate from 'url-template'
import { normalizeEntityUri } from './normalizeUri'
import storeValueProxy from './storeValueProxy'
import store from './index'

axios.defaults.withCredentials = true
Vue.use(VueAxios, axios)

export const API_ROOT = window.environment.API_ROOT_URL

/**
 * Error class for returning server exceptions (attaches response object to error)
 * @param response        Axios response object
 * @param ...params       Any other parameters from default Error constructor (message, etc.)
 */
export class ServerException extends Error {
  constructor (response, ...params) {
    super(...params)

    if (!this.message) {
      this.message = 'Server-Error ' + response.status + ' (' + response.statusText + ')'
    }
    this.name = 'ServerException'
    this.response = response
  }
}

/**
 * Processes error object received from Axios for further usage. Triggers delete chain as side effect.
 * @param uri             Requested URI that triggered the error
 * @param error           Raw error object received from Axios
 * @returns Error         Return new error object with human understandable error message
 */
function handleAxiosError (uri, error) {
  // Server Error (response received but with error code)
  if (error.response) {
    const response = error.response

    // 404 Entity not found error
    if (response.status === 404) {
      store.commit('deleting', uri)
      deleted(uri) // no need to wait for delete operation to finish
      // return new ServerException(response, `Could not perform operation, "${uri}" has been deleted`)
      return new Error(`Could not perform operation, "${uri}" has been deleted`)

    // 403 Permission error
    } else if (response.status === 403) {
      return new ServerException(response, 'No permission to perform operation')

    // API Problem
    } else if (response.headers['content-type'] === 'application/problem+json') {
      return new ServerException(response, 'Server-Error ' + response.status + ' (' + response.data.detail + ')')

    // other unknown server error (not of type application/problem+json)
    } else {
      return new ServerException(response)
    }
  // another error (most probably connection timeout; no response received)
  } else {
    return new Error('Could not connect to server. Check your internet connection and try again.')
  }
}

/**
 * Sends a POST request to the backend, in order to create a new entity. Note that this does not
 * reload any collections that this new entity might be in, the caller has to do that on its own.
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
    storeHalJsonData(data)
    return get(data._links.self.href)
  }, (error) => {
    throw handleAxiosError(uri, error)
  }))
}

/**
 * Reloads an entity from the API.
 *
 * @param uriOrEntity URI (or instance) of an entity to reload from the API
 * @returns Promise   Resolves when the GET request has completed and the updated entity is available
 *                    in the Vuex store.
 */
export const reload = function (uriOrEntity) {
  return get(uriOrEntity, true)._meta.load
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
 *   activityTypes () { return this.oneSpecificCamp.activityTypes() }
 *   user () { return this.api.get().profile() } // Root endpoint ('/') and navigate through self-discovery API
 * },
 * created () {
 *   this.oneSpecificCamp._meta.load.then(() => {
 *     // do something now that the camp is loaded from the API
 *   })
 * }
 *
 * @param uriOrEntity URI (or instance) of an entity to load from the store or API
 * @param forceReload If true, the entity will be fetched from the API even if it is already in the Vuex store.
 *                    Note that the function will still return the old value in this case, but you can
 *                    wait for the new value using the ._meta.load promise.
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
 * sets the load promise on the entity in the Vuex store.
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
    // Reuse the loading entity and load promise that is already waiting for a pending API request
    return store.state.api[uri]
  }

  let dataFinishedLoading = Promise.resolve(store.state.api[uri])
  if (!existsInStore || forceReload) {
    dataFinishedLoading = loadFromApi(uri)
  } else if (store.state.api[uri]._meta.load) {
    // reuse the existing promise from the store if possible
    dataFinishedLoading = store.state.api[uri]._meta.load
  }

  // We mutate the store state here without telling Vuex about it, so it won't complain and won't make load reactive.
  // The promise is needed in the store for some special cases when a loading entity is requested a second time with
  // this.api.get(...) or this.api.reload(...).
  store.state.api[uri]._meta.load = markAsDoneWhenResolved(dataFinishedLoading)

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
      (error) => {
        reject(handleAxiosError(uri, error))
      }
    )
  })
}

/**
 * Loads the URI of a related entity from the store, or the API in case it is not already fetched.
 *
 * @param uriOrEntity    URI (or instance) of an entity from the API
 * @param relation       the name of the relation for which the URI should be retrieved
 * @param templateParams in case the relation is a templated link, the template parameters that should be filled in
 * @returns Promise      resolves to the URI of the related entity.
 */
export const href = async function (uriOrEntity, relation, templateParams = {}) {
  const self = normalizeEntityUri(await get(uriOrEntity)._meta.load, API_ROOT)
  const rel = store.state.api[self][relation]
  if (!rel || !rel.href) return undefined
  if (rel.templated) {
    return urltemplate.parse(rel.href).expand(templateParams)
  }
  return API_ROOT + rel.href
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
  const existsInStore = (uri in store.state.api)

  if (!existsInStore) {
    store.commit('addEmpty', uri)
  }

  store.state.api[uri]._meta.load = markAsDoneWhenResolved(axios.patch(API_ROOT + uri, data).then(({ data }) => {
    // Workaround because API adds page parameter even to first page when it was not requested that way
    // TODO fix backend API and remove the next line
    data._links.self.href = uri
    storeHalJsonData(data)
    return get(uri)
  }, (error) => {
    throw handleAxiosError(uri, error)
  }))

  return store.state.api[uri]._meta.load
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
 * Removes all stored entities from the Vuex store (but does not delete them using the API).
 */
export const purgeAll = function () {
  store.commit('purgeAll')
}

/**
 * Attempts to permanently delete a single entity using a DELETE request to the API.
 * This function performs the following operations when given the URI of an entity E:
 * 1. Marks E in the Vuex store with the ._meta.deleting flag
 * 2. Sends a DELETE request to the API in order to delete E from the backend (in case of failure, the
 *    deleted flag is reset and the operation is aborted)
 * 3. Finds all entities [...R] in the store that reference E (e.g. find the corresponding camp when
 *    deleting an activity) and reloads them from the API
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
  return markAsDoneWhenResolved(axios.delete(API_ROOT + uri).then(
    () => deleted(uri),
    (error) => {
      store.commit('deletingFailed', uri)
      throw handleAxiosError(uri, error)
    }
  ))
}

function valueIsArrayWithReferenceTo (value, uri) {
  return Array.isArray(value) && value.some(entry => valueIsReferenceTo(entry, uri))
}

function valueIsReferenceTo (value, uri) {
  if (value === null) return false

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
  return Promise.all(findEntitiesReferencing(uri)
    // don't reload entities that are already being deleted, to break circular dependencies
    .filter(outdatedEntity => !outdatedEntity._meta.deleting)
    .map(outdatedEntity => reload(outdatedEntity))
  ).then(() => purge(uri))
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
    filterReferences: true,
    embeddedStandaloneCollectionKey: 'items'
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
class ApiStorePlugin {
  install (Vue, options) {
    Object.defineProperties(Vue.prototype, {
      api: {
        get () { return { post, get, reload, del, patch, purge, href } }
      }
    })
  }
}

export default new ApiStorePlugin()
