import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
import VueAxios from 'vue-axios'
import normalize from 'hal-json-normalizer'
import { normalizeUri } from '@/store/uriUtils'
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
  }
}

export default new Vuex.Store({
  state,
  mutations,
  strict: process.env.NODE_ENV !== 'production'
})

export const get = function (vm, uri) {
  uri = normalizeUri(uri)
  if (!(uri in vm.$store.state.api)) {
    vm.$store.commit('addEmpty', uri)
    vm.axios.get(API_ROOT + uri).then(({ data }) => {
      // Workaround because API adds page parameter even to first page when it was not requested that way
      // TODO fix backend API and remove the next line
      data._links.self.href = uri
      storeHalJsonData(vm, data)
    })
  }
  return storeValueProxy(vm, vm.$store.state.api[uri])
}

function storeHalJsonData (vm, data) {
  const normalizedData = normalize(data, { normalizeUri: (uri) => normalizeUri(uri, API_ROOT), filterReferences: true })
  vm.$store.commit('add', normalizedData)
}

Object.defineProperties(Vue.prototype, {
  api: {
    get () {
      return {
        get: uri => get(this, uri)
      }
    }
  }
})
