import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
import VueAxios from 'vue-axios'
import HalJsonVuex from 'hal-json-vuex'
import lang from './lang'

Vue.use(Vuex)

const store = new Vuex.Store({
  modules: {
    lang
  },
  strict: process.env.NODE_ENV !== 'production'
})

axios.defaults.withCredentials = true
axios.defaults.baseURL = window.environment.API_ROOT_URL
Vue.use(VueAxios, axios)

export const apiStore = HalJsonVuex(store, axios, { forceRequestedSelfLink: true })
Vue.use(apiStore)

export default store
