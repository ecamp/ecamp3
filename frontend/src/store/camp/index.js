import axios from 'axios'
import halfred from 'halfred'
import Vue from 'vue'

export default {
  namespaced: true,
  state: {
    camps: []
  },
  mutations: {
    setCamp (state, payload) {
      Vue.set(state.camps, payload.id, payload)
    }
  },
  actions: {
    async fetchById ({ commit, state }, payload) {
      if (!state.camps[payload.id] || payload.forceReload) { // only load if data is missing or if `forceReload` is set
        commit('setLoading', true, { root: true })
        commit('clearError', null, { root: true })

        try {
          const response = await axios.get(process.env.VUE_APP_ROOT_API + '/camp/' + payload.id)
          commit('setCamp', halfred.parse(response.data))
        } catch (error) {
          commit('setError', 'Could not load camp. ' + error, { root: true })
        }

        commit('setLoading', false, { root: true })
      }
    }
  }
}
