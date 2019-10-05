import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
import VueAxios from 'vue-axios'
import normalize from 'hal-json-normalizer'
import { normalizeUri } from '@/store/uriUtils'
import storeValueProxy, { loadingProxy } from '@/store/storeValueProxy'

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
  deleted(state, uri) {
    Vue.set(state.api, uri, { _meta: { self: uri, deleted: true } })
  }
}

export default new Vuex.Store({
  state,
  mutations,
  strict: process.env.NODE_ENV !== 'production'
})

function getNormalizedUri(uriOrObject) {
  return normalizeUri(typeof uriOrObject === 'string' ? uriOrObject : (uriOrObject._meta || {}).self)
}

const get = function (vm, uriOrObject, forceReload = false) {
  const uri = getNormalizedUri(uriOrObject)
  if (uri === null) {
    // We don't even know the URI, so return something that doesn't break the UI.
    // Hopefully this is running inside a reactive method that will be re-calculated once the URI is known.
    return loadingProxy()
  }
  const existsInStore = (uri in vm.$store.state.api)
  if (forceReload || !existsInStore) {
    if (!existsInStore) {
      vm.$store.commit('addEmpty', uri)
    }
    vm.axios.get(API_ROOT + uri).then(({ data }) => {
      // Workaround because API adds page parameter even to first page when it was not requested that way
      // TODO fix backend API and remove the next line
      data._links.self.href = uri
      storeHalJsonData(vm, data)
    }, ({response}) => {
      if (response.status === 404) {
        vm.$store.commit('deleted', uri);
      }
    })
  }
  const obj = vm.$store.state.api[uri]
  if (obj._meta.deleted) {
    return null
  } else {
    return storeValueProxy(vm, obj)
  }
}

const purge = function (vm, uriOrObject) {
  const uri = getNormalizedUri(uriOrObject)
  if (uri === null) {
    // Can't purge an unknown URI, do nothing
    return
  }
  vm.$store.commit('purge', uri)
}

const del = function (vm, uriOrObject) {
  const uri = getNormalizedUri(uriOrObject)
  if (uri === null) {
    // Can't delete an unknown URI, do nothing
    return
  }
  vm.axios.delete(API_ROOT + uri).then(() => {
    vm.$store.commit('deleted', uri)
  })
}

function storeHalJsonData (vm, data) {
  const normalizedData = normalize(data, {
    camelizeKeys: false,
    metaKey: '_meta',
    normalizeUri: (uri) => normalizeUri(uri, API_ROOT),
    filterReferences: true
  })
  vm.$store.commit('add', normalizedData)
}

Object.defineProperties(Vue.prototype, {
  api: {
    get () {
      return {
        get: uriOrObject => get(this, uriOrObject),
        purge: uriOrObject => purge(this, uriOrObject),
        reload: uriOrObject => get(this, uriOrObject, true),
        del: uriOrObject => del(this, uriOrObject),
      }
    }
  }
})
