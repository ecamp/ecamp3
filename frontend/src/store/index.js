import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
import VueAxios from 'vue-axios'

Vue.use(Vuex)
axios.defaults.withCredentials = true
Vue.use(VueAxios, axios)

const API_ROOT = process.env.VUE_APP_ROOT_API

export const state = {
  // TODO find a way to split the API state up and dynamically add independent state entries for each entity
  api: {}
}

export const mutations = {
  addEmpty (state, uri) {
    Vue.set(state.api, uri, { _loading: true, self: uri })
  },
  add (state, data) {
    Vue.set(state.api, data.self, data)
  }
}

export default new Vuex.Store({
  state,
  mutations,
  strict: process.env.NODE_ENV !== 'production'
})

function requestFromApi (vm, uri) {
  vm.$store.commit('addEmpty', uri)
  vm.axios.get(API_ROOT + uri).then(({ data }) => {
    replaceRelationsWithURIs(vm, data)
  })
  return vm.$store.state.api[uri]
}

function replaceRelationsWithURIs (vm, data) {
  Object.keys(data).forEach(key => {
    if (data[key].hasOwnProperty('_links')) {
      // embedded single entity
      let uri = replaceRelationsWithURIs(vm, data[key])
      data[key] = () => vm.api(uri)
    } else if (Array.isArray(data[key])) {
      // embedded collection (not paginated, full list)
      data[key].forEach((entry, index) => {
        let uri = replaceRelationsWithURIs(vm, entry)
        data[key][index] = () => vm.api(uri)
      })
    }
  })
  if (data.hasOwnProperty('_links')) {
    Object.keys(data._links).forEach(key => {
      // linked single entity, collection or self
      if (key === 'self') {
        data[key] = normalizedUri(data._links[key].href)
      } else {
        let uri = data._links[key].href
        data[key] = () => vm.api(uri)
      }
    })
    delete data._links
  }
  if (data.hasOwnProperty('_embedded')) {
    // page of a collection
    data.items = data._embedded.items
    data.items.forEach((item, index) => {
      let uri = replaceRelationsWithURIs(vm, item)
      data.items[index] = () => vm.api(uri)
    })
    delete data._embedded
  }
  vm.$store.commit('add', data)
  return data.self
}

function normalizedUri (uri) {
  // TODO normalize the order of query parameters so it does not matter when paginating
  if (!uri) {
    return '/'
  }
  if (uri.startsWith(API_ROOT)) {
    return uri.substr(API_ROOT.length)
  }
  return uri
}

export const api = function (uri) {
  uri = normalizedUri(uri)
  return this.$store.state.api[uri] || requestFromApi(this, uri)
}

Vue.mixin({
  methods: { api }
})
