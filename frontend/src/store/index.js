import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
import VueAxios from 'vue-axios'
import Collection from '@/store/collection'
import { hasQueryParam, sortQueryParams } from '@/store/uriUtils'

Vue.use(Vuex)
axios.defaults.withCredentials = true
Vue.use(VueAxios, axios)

const API_ROOT = process.env.VUE_APP_ROOT_API

export const state = {
  api: {}
}

export const mutations = {
  addEmpty (state, { uri, loaded }) {
    const normalizedUri = normalizeUri(uri)
    Vue.set(state.api, normalizeUri(uri), { _meta: { loading: true, self: normalizedUri, loaded } })
  },
  add (state, data) {
    const normalizedUri = normalizeUri(data._meta.self)
    data._meta.loaded = new Promise(resolve => resolve(state.api[normalizedUri]))
    Vue.set(state.api, normalizedUri, data)
  },
  addCollectionItem (state, { collectionUri, item }) {
    const normalizedUri = normalizeUri(collectionUri)
    state.api[normalizedUri].items.push(item)
  }
}

export default new Vuex.Store({
  state,
  mutations,
  strict: process.env.NODE_ENV !== 'production'
})

export const api = function (uri) {
  uri = normalizeUri(uri)
  if (!(uri in this.$store.state.api)) {
    this.$store.commit('addEmpty', {
      uri,
      loaded: new Promise((resolve) => {
        this.axios.get(API_ROOT + uri).then(({ data }) => {
          let referenceToStoredData = parseAndStoreHalJsonData(this, data)
          resolve(referenceToStoredData())
        })
      })
    })
  }
  return this.$store.state.api[uri]
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

function hasEmbeddedItems (data) {
  return hasEmbedded(data) && data._embedded.hasOwnProperty('items') && isList(data._embedded.items)
}

export function isSinglePage (data) {
  return hasSelfLink(data) && hasQueryParam(data._links.self.href, 'page')
}

function isList (data) {
  return Array.isArray(data)
}

function isPrimitive (data) {
  return !hasSelfLink(data)
}

function isReference (data) {
  return !hasIdProperty(data) && !hasEmbedded(data)
}

function isCollection (data) {
  return hasEmbeddedItems(data) && !isSinglePage(data)
}

/**
 * Stores data into the Vuex store and returns a function that can be used to access the stored data.
 * Works recursively on nested (embedded, linked or referenced) objects and arrays.
 * @param {Vue} vm the global Vue instance
 * @param {Object} data to be stored in the Vuex store
 * @returns {Function|Number|String|Object|Array} accessor function that can be called to retrieve the data from the Vuex store, or literal value if no self link is found.
 */
function parseAndStoreHalJsonData (vm, data) {
  if (isList(data)) {
    return parseEmbeddedCollection(vm, data)
  }

  if (isPrimitive(data)) {
    return data
  }

  if (isReference(data)) {
    return parseReference(vm, data)
  }

  if (isCollection(data)) {
    commitToStore(vm, asFirstPage(vm, data))
    return commitToStore(vm, autoPaginatingCollection(vm, data))
  }

  return commitToStore(vm, parseObject(vm, data))
}

function parseObject (vm, data) {
  Object.keys(data).filter(key => key !== '_meta').forEach(key => {
    data[key] = parseAndStoreHalJsonData(vm, data[key])
  })

  copySelfLinkToMeta(data)
  Object.entries(data._links).forEach(([key, { href: uri }]) => {
    if (data.hasOwnProperty(key)) {
      console.warn('Overwriting existing property \'' + key + '\' with property from _links.')
    }
    let linkedUri = normalizeUri(uri)
    data[key] = () => vm.api(linkedUri)
  })

  Object.keys(data._embedded || {}).forEach(key => {
    if (data.hasOwnProperty(key)) {
      console.warn('Overwriting existing property \'' + key + '\' with property from _embedded.')
    }
    if (key === 'items') {
      data[key] = data._embedded[key].map(entry => parseAndStoreHalJsonData(vm, entry))
    } else {
      data[key] = parseAndStoreHalJsonData(vm, data._embedded[key])
    }
  })

  removeLinksAndEmbedded(data)

  return data
}

function parseEmbeddedCollection (vm, data) {
  const embeddedList = Collection.fromArray(data.map(entry => parseAndStoreHalJsonData(vm, entry)))
  return () => embeddedList
}

function deepCloneJsonData (data) {
  return JSON.parse(JSON.stringify(data))
}

function asFirstPage (vm, data) {
  const page = deepCloneJsonData(data)
  page._links.self = page._links.first
  return parseObject(vm, page)
}

function autoPaginatingCollection (vm, data) {
  const parsedData = parseObject(vm, data)
  return Collection.fromPage(parsedData,
    item => vm.$store.commit('addCollectionItem', { collectionUri: parsedData._meta.self, item }))
}

function parseReference (vm, data) {
  const referenceUri = normalizeUri(data._links.self.href)
  return () => vm.api(referenceUri)
}

function copySelfLinkToMeta (data) {
  if (!data.hasOwnProperty('_meta')) {
    data._meta = {}
  }
  data._meta.self = data._links.self.href
}

function removeLinksAndEmbedded (data) {
  delete data._links
  delete data._embedded
}

function commitToStore (vm, data) {
  vm.$store.commit('add', data)
  const uri = normalizeUri(data._meta.self)
  return () => vm.api(uri)
}

function normalizeUri (uri) {
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
