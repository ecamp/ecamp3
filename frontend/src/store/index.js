import Vue from 'vue'
import Vuex from 'vuex'

import shared from './shared'
import camps from './camps'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    shared,
    camps
  }
})
