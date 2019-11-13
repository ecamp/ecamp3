import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
import VueAxios from 'vue-axios'
import normalize from 'hal-json-normalizer'
import { normalizeObjectUri } from '@/store/uriUtils'
import storeValueProxy from '@/store/storeValueProxy'

Vue.use(Vuex)
axios.defaults.withCredentials = true
Vue.use(VueAxios, axios)

const API_ROOT = process.env.VUE_APP_ROOT_API

export const state = {
  api: {}
}

export const mutations = {
  addEmpty (state, uri) {
    Vue.set(state.api, uri, { _meta: { self: uri, loading: true } })
  },
  add (state, data) {
    Object.keys(data).forEach((uri) => {
      Vue.set(state.api, uri, data[uri])
    })
  },
  purge (state, uri) {
    Vue.delete(state.api, uri)
  },
  deleting (state, uri) {
    Vue.set(state.api[uri]._meta, 'deleting', true)
  },
  deletingFailed (state, uri) {
    Vue.set(state.api[uri]._meta, 'deleting', false)
  }
}

export default new Vuex.Store({
  state,
  mutations,
  strict: process.env.NODE_ENV !== 'production'
})

const post = function (vm, uriOrObject, data) {
  const uri = normalizeObjectUri(uriOrObject)

  vm.axios.post(API_ROOT + uri, data).then(({ data }) => {
    // Workaround because API adds page parameter even to first page when it was not requested that way
    // TODO fix backend API and remove the next line
    data._links.self.href = uri
    storeHalJsonData(vm, data)
  })
}

const get = function (vm, uriOrObject, forceReload = false) {
  const uri = normalizeObjectUri(uriOrObject)
  if (uri === null) {
    if (uriOrObject[Symbol('isLoadingProxy')]) {
      // A loadingProxy is safe to return without breaking the UI.
      return uriOrObject
    }
    // We don't know anything about the requested object.
    throw new Error(`Could not interpret "${uriOrObject}" as an object or URI`)
  }
  const existsInStore = (uri in vm.$store.state.api)
  const isLoading = existsInStore && (vm.$store.state.api[uri]._meta || {}).loading
  if (!existsInStore) {
    vm.$store.commit('addEmpty', uri)
  }
  let loaded
  if (isLoading && !forceReload) {
    // Reuse the existing promise that is already waiting for a pending API request
    loaded = vm.$store.state.api[uri]._meta.loaded
  } else if (!existsInStore || forceReload) {
    loaded = new Promise((resolve, reject) => {
      vm.axios.get(API_ROOT + uri).then(
        ({ data }) => {
          // Workaround because API adds page parameter even to first page when it was not requested that way
          // TODO fix backend API and remove the next line
          data._links.self.href = uri
          storeHalJsonData(vm, data)
          resolve(vm.$store.state.api[uri])
        },
        ({ response }) => {
          if (response.status === 404) {
            deleted(vm, uri)
          }
          reject(response)
        })
    })
  } else {
    loaded = Promise.resolve(vm.$store.state.api[uri])
  }
  return storeValueProxy(vm, vm.$store.state.api[uri], loaded)
}

const patch = function (vm, uriOrObject, data) {
  const uri = normalizeObjectUri(uriOrObject)
  if (uri === null) {
    // Can't patch an unknown URI, do nothing
    return
  }
  vm.axios.patch(API_ROOT + uri, data).then(({ data }) => {
    // Workaround because API adds page parameter even to first page when it was not requested that way
    // TODO fix backend API and remove the next line
    data._links.self.href = uri
    storeHalJsonData(vm, data)
  }, ({ response }) => {
    if (response.status === 404) {
      deleted(vm, uri)
    }
  })
}

const purge = function (vm, uriOrObject) {
  const uri = normalizeObjectUri(uriOrObject)
  if (uri === null) {
    // Can't purge an unknown URI, do nothing
    return
  }
  vm.$store.commit('purge', uri)
}

const del = function (vm, uriOrObject) {
  const uri = normalizeObjectUri(uriOrObject)
  if (uri === null) {
    // Can't delete an unknown URI, do nothing
    return
  }
  vm.$store.commit('deleting', uri)
  return vm.axios.delete(API_ROOT + uri)
    .then(() => deleted(vm, uri), ({ response }) => deletingFailed(vm, uri, response))
}

function valueIsArrayWithReferenceTo (value, uri) {
  return Array.isArray(value) && value.some(entry => valueIsReferenceTo(entry, uri))
}

function valueIsReferenceTo (value, uri) {
  const objectKeys = Object.keys(value)
  return objectKeys.length === 1 && objectKeys[0] === 'href' && value.href === uri
}

function findEntitiesReferencing (vm, uri) {
  return Object.values(vm.$store.state.api)
    .filter((entity) => {
      return Object.values(entity).some(propertyValue =>
        valueIsReferenceTo(propertyValue, uri) || valueIsArrayWithReferenceTo(propertyValue, uri)
      )
    })
}

function deleted (vm, uri) {
  Promise.all(findEntitiesReferencing(vm, uri).map(outdatedEntity => vm.api.reload(outdatedEntity)._meta.loaded))
    .then(() => vm.api.purge(uri))
}

function deletingFailed (vm, uri, response) {
  if (response.status !== 204) {
    vm.$store.commit('deletingFailed', uri)
  }
}

function storeHalJsonData (vm, data) {
  const normalizedData = normalize(data, {
    camelizeKeys: false,
    metaKey: '_meta',
    normalizeUri: (uri) => normalizeObjectUri(uri, API_ROOT),
    filterReferences: true
  })
  vm.$store.commit('add', normalizedData)
}

Object.defineProperties(Vue.prototype, {
  api: {
    get () {
      return {
        post: (uriOrObject, data) => post(this, uriOrObject, data),
        get: uriOrObject => get(this, uriOrObject),
        reload: uriOrObject => get(this, uriOrObject, true),
        del: uriOrObject => del(this, uriOrObject),
        patch: (uriOrObject, data) => patch(this, uriOrObject, data),
        purge: uriOrObject => purge(this, uriOrObject)
      }
    }
  }
})
