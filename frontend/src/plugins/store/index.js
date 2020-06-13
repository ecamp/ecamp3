import Vue from 'vue'
import Vuex from 'vuex'
import api from './api'
import lang from './lang'

Vue.use(Vuex)

const store = new Vuex.Store({
  modules: {
    api,
    lang
  },
  strict: process.env.NODE_ENV !== 'production'
})
export default store
