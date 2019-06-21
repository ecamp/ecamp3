import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
import VueAxios from 'vue-axios'

Vue.use(Vuex)
axios.defaults.withCredentials = true
Vue.use(VueAxios, axios)

const API_ROOT = process.env.VUE_APP_ROOT_API

export default new Vuex.Store({
  state: {
    // TODO find a way to split the API state up and dynamically add independent state entries for each entity
    api: {}
  },

  mutations: {
    addEmpty (state, uri) {
      Vue.set(state.api, uri, { _loading: true })
    },
    add (state, { uri, data }) {
      Vue.set(state.api, uri, data)
    }
  },

  strict: process.env.NODE_ENV !== 'production'
})

async function fetchFromAPI (store, uri) {
  store.commit('addEmpty', uri)
  let data = await Vue.axios.get(uri)
  // TODO replace embedded and linked entities (anything that has a self-link) with store pointers
  // TODO save Proxy objects instead of raw data in order to automatically resolve store pointers when accessing properties
  store.commit('add', { uri, data })
}

Vue.mixin({
  computed: {
    api: function ({ apiPath, start }) {
      if (!start) {
        start = API_ROOT
      } else if (typeof start === 'object') {
        start = start._links.self.href
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
