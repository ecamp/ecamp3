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
    Vue.set(state.api, uri, { _loading: true })
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

async function fetchFromAPI (store, uri) {
  store.commit('addEmpty', uri)
  let data = await Vue.axios.get(uri).data
  store.commit('add', replaceRelationsWithURIs(data))
  // TODO save Proxy objects instead of raw data in order to automatically resolve store pointers when accessing properties
}

function replaceRelationsWithURIs ({ data }) {
  let toAdd = []
  Object.keys(data).forEach(key => {
    if (data[key].hasOwnProperty('_links')) {
      // embedded single entity
      toAdd.concat(replaceRelationsWithURIs({ data: data[key] }))
      data[key] = data[key].self
    } else if (Array.isArray(data[key])) {
      // embedded collection (not paginated, full list)
      data[key].forEach((entry, index) => {
        toAdd.concat(replaceRelationsWithURIs({ data: entry }))
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
      toAdd.concat(replaceRelationsWithURIs({ data: item }))
      // TODO normalize URIs so the order of query parameters does not matter when paginating
      data.items[index] = item.self
    })
    delete data._embedded
  }
  toAdd.push(data)
  return toAdd
}

Vue.mixin({
  computed: {
    api: function ({ apiPath, start }) {
      if (!start) {
        start = API_ROOT
      } else if (typeof start === 'object') {
        start = start.self
      }
      if (!this.$store.state.api.hasOwnProperty(start)) {
        fetchFromAPI(start)
      }
      if (!apiPath || apiPath.length === 0) {
        return this.$store.state.api[start]
      }
      return this.api({ apiPath: apiPath.slice(1), start: this.$store.state.api[start][apiPath[0]] })
    }
  }
})
