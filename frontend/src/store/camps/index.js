import axios from 'axios'
import halfred from 'halfred'

export default {
  namespaced: true,
  state: {
    camps: []
  },
  mutations: {
    setCamps (state, payload) {
      state.camps = payload
    }
  },
  actions: {
    async fetchAll ({ commit }) {
      commit('setLoading', true, { root: true })
      commit('clearError', null, { root: true })

      try {
        const response = await axios.get(process.env.VUE_APP_ROOT_API + '/camp')
        commit('setCamps', halfred.parse(response.data).embeddedResourceArray('items'))
      } catch (error) {
        commit('setError', 'Could not get camp list. ' + error, { root: true })
      }

      commit('setLoading', false, { root: true })
    }
  }
}
