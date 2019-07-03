import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
import VueAxios from 'vue-axios'
import Collection from './collection'

Vue.use(Vuex)
axios.defaults.withCredentials = true
Vue.use(VueAxios, axios)

const API_ROOT = process.env.VUE_APP_ROOT_API

export const state = {
  api: {}
}

export const mutations = {
  addEmpty (state, { uri, loaded }) {
    Vue.set(state.api, uri, { _loading: true, self: uri, loaded })
  },
  add (state, data) {
    Vue.set(state.api, data.self, { ...data, loaded: new Promise(resolve => resolve(state.api[data.self])) })
  },
  appendCollectionItem (state, { collectionUri, item }) {
    state.api[collectionUri].items.push(item)
  }
}

export default new Vuex.Store({
  state,
  mutations,
  strict: process.env.NODE_ENV !== 'production'
})

function storeHalJsonData (vm, data) {
  Object.keys(data).forEach(key => {
    if (data[key].hasOwnProperty('_links')) {
      // embedded single entity, replace by accessor function
      data[key] = storeHalJsonData(vm, data[key])
    } else if (Array.isArray(data[key])) {
      // embedded collection (not paginated, full list), replace by accessor function for collection of accessor functions
      let collection = Collection.fromArray(data[key].map(entry => storeHalJsonData(vm, entry)))
      data[key] = () => collection
    }
  })
  if (data.hasOwnProperty('_links')) {
    Object.entries(data._links).forEach(([key, { href: uri }]) => {
      if (key === 'self') {
        // self link, keep as URI
        data[key] = normalizedUri(uri)
      } else {
        // linked single entity or collection, replace by accessor function
        data[key] = () => vm.api(uri)
      }
    })
    delete data._links
  }
  if (data.hasOwnProperty('_embedded')) {
    // page of a collection, replace by collection of accessor functions
    data.items = data._embedded.items.map(item => storeHalJsonData(vm, item))
    delete data._embedded
    data = Collection.fromPage(data, item => vm.$store.commit('appendCollectionItem', { item, collectionUri: data.self }))
  }
  vm.$store.commit('add', data)
  return () => vm.api(data.self)
}

export function sortQueryParams (uri) {
  let queryStart = uri.indexOf('?')
  if (queryStart === -1) return uri
  let prefix = uri.substring(0, queryStart + 1)
  let query = new URLSearchParams(uri.substring(queryStart + 1))
  let sortedQuery = new URLSearchParams()
  for (const key of [ ...new Set(query.keys()) ].sort()) {
    for (const value of query.getAll(key)) {
      sortedQuery.append(key, value)
    }
  }
  return prefix + sortedQuery.toString()
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

Vue.mixin({
  methods: { api }
})

export { Collection }
