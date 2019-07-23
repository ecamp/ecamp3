import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
import VueAxios from 'vue-axios'
import Collection, { getFullList, PAGE_QUERY_PARAM } from '@/store/collection'
import { sortQueryParams, API_ROOT, hasQueryParam } from '@/store/uriUtils'

Vue.use(Vuex)
axios.defaults.withCredentials = true
Vue.use(VueAxios, axios)

export const state = {
  api: {}
}

export const mutations = {
  addEmpty (state, { uri, loaded }) {
    Vue.set(state.api, uri, { _meta: { loading: true, self: uri, loaded } })
  },
  add (state, data) {
    data._meta.loaded = new Promise(resolve => resolve(state.api[data._meta.self]))
    Vue.set(state.api, data._meta.self, data)
  }
}

export default new Vuex.Store({
  state,
  mutations,
  strict: process.env.NODE_ENV !== 'production'
})

export const api = function (uri) {
  uri = normalizedUri(uri)
  if (!(uri in this.$store.state.api)) {
    this.$store.commit('addEmpty', {
      uri,
      loaded: new Promise((resolve) => {
        this.axios.get(API_ROOT + uri).then(({ data }) => {
          let referenceToStoredData = storeHalJsonData(this, data)
          resolve(referenceToStoredData())
        })
      })
    })
  }
  return this.$store.state.api[uri]
}

function isList (data) {
  return Array.isArray(data)
}

function hasLinks (data) {
  return data.hasOwnProperty('_links')
}

function hasSelfLink (data) {
  return hasLinks(data) && data._links.hasOwnProperty('self')
}

function hasIdProperty (data) {
  return data.hasOwnProperty('id')
}

function hasEmbedded (data) {
  return data.hasOwnProperty('_embedded')
}

/**
 * Stores data into the Vuex store and returns a function that can be used to access the stored data.
 * Works recursively on nested (embedded, linked or referenced) objects and arrays.
 * @param {Vue} vm the global Vue instance
 * @param {Object} data to be stored in the Vuex store
 * @returns {Function|Number|String|Object|Array} accessor function that can be called to retrieve the data from the Vuex store, or literal value if no self link is found.
 */
function storeHalJsonData (vm, data) {
  if (isList(data)) {
    return storeEmbeddedList(vm, data)
  }

  if (!hasSelfLink(data)) {
    return storePrimitive(vm, data)
  }

  if (hasIdProperty(data)) {
    return storeObject(vm, data)
  }

  if (hasEmbedded(data)) {
    return storeListPage(vm, data)
  }

  return storeReference(vm, data)
}

function storePrimitive (vm, data) {
  return data
}

function storeObject (vm, data) {
  Object.keys(data).forEach(key => {
    data[key] = storeHalJsonData(vm, data[key])
  })

  copySelfLinkToMeta({ data })
  Object.entries(data._links).forEach(([key, { href: uri }]) => {
    data[key] = () => vm.api(uri)
  })

  Object.keys(data._embedded || {}).forEach(key => {
    data[key] = storeHalJsonData(vm, data._embedded[key])
  })

  removeLinksAndEmbedded({ data })

  return commitToStore(vm, data)
}

function storeEmbeddedList (vm, data) {
  let collection = Collection.fromArray(data.map(entry => storeHalJsonData(vm, entry)))
  return () => collection
}

function storeListPage (vm, data) {
  if (hasQueryParam(data._links.self.href, PAGE_QUERY_PARAM)) {
    storeObject(vm, [...data])
  }
  return storeObject(vm, getFullList(vm, data))
}

function storeReference (vm, data) {
  return () => vm.api(data._links.self.href)
}

function copySelfLinkToMeta ({ data }) {
  if (!data.hasOwnProperty('_meta')) {
    data._meta = {}
  }
  data._meta.self = data._links.self.href
}

function removeLinksAndEmbedded ({ data }) {
  delete data._links
  delete data._embedded
}

function commitToStore (vm, data) {
  vm.$store.commit('add', data)
  return () => vm.api(data._meta.self)
}

function normalizedUri (uri) {
  if (!uri) {
    return '/'
  }
  uri = sortQueryParams(uri)
  if (uri.startsWith(API_ROOT)) {
    return uri.substr(API_ROOT.length)
  }
  return uri
}

Vue.mixin({
  methods: { api }
})
