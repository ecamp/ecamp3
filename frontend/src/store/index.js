import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
import VueAxios from 'vue-axios'

Vue.use(Vuex)
axios.defaults.withCredentials = true
Vue.use(VueAxios, axios)

const API_ROOT = process.env.VUE_APP_ROOT_API

const state = {
  // TODO find a way to split the API state up and dynamically add independent state entries for each entity
  api: {}
}

export const mutations = {
  addEmpty (state, uri) {
    Vue.set(state.api, uri, { _loading: true, self: uri })
  },
  add (state, dataArray) {
    dataArray.forEach(entry => {
      Vue.set(state.api, entry.self, entry)
    })
  }
}

export default new Vuex.Store({
  state,
  mutations,
  strict: process.env.NODE_ENV !== 'production'
})

async function requestFromApi ({ $store, axios }, uri) {
  $store.commit('addEmpty', uri)
  let { data } = await axios.get(API_ROOT + uri)
  $store.commit('add', replaceRelationsWithURIs({ data }))
  // TODO save Proxy objects instead of raw data in order to automatically resolve store pointers when accessing properties
}

function replaceRelationsWithURIs ({ data }) {
  let toAdd = []
  Object.keys(data).forEach(key => {
    if (data[key].hasOwnProperty('_links')) {
      // embedded single entity
      toAdd.push(...replaceRelationsWithURIs({ data: data[key] }))
      data[key] = data[key].self
    } else if (Array.isArray(data[key])) {
      // embedded collection (not paginated, full list)
      data[key].forEach((entry, index) => {
        toAdd.push(...replaceRelationsWithURIs({ data: entry }))
        data[key][index] = entry.self
      })
    }
  })
  if (data.hasOwnProperty('_links')) {
    Object.keys(data._links).forEach(key => {
      // linked single entity, collection or self
      data[key] = data._links[key].href
    })
    delete data._links
  }
  if (data.hasOwnProperty('_embedded')) {
    // page of a collection
    data.items = data._embedded.items
    data.items.forEach((item, index) => {
      toAdd.push(...replaceRelationsWithURIs({ data: item }))
      data.items[index] = item.self
    })
    delete data._embedded
  }
  toAdd.push(data)
  return toAdd
}

function normalizeUri (uri) {
  // TODO normalize the order of query parameters so it does not matter when paginating
  if (!uri) {
    return '/'
  } else if (typeof uri === 'object') {
    uri = uri.self
  }
  if (uri.startsWith(API_ROOT)) {
    return uri.substr(API_ROOT.length)
  }
  return uri
}

export const api = function (uri) {
  uri = normalizeUri(uri)
  return this.$store.state.api[uri] || requestFromApi(this, uri)
}

Vue.mixin({
  methods: { api }
})
