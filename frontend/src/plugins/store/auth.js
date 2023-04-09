import { apiStore } from '@/plugins/store/index'

export const state = {
  user: null,
}

export const mutations = {
  login(state, user) {
    state.user = user
  },

  logout(state) {
    state.user = null
  },
}
export const getters = {
  getLoggedInUser: (authState, _getters, _rootState, rootGetters) => {
    if (!authState.user) return authState.user
    // Get the URI of the user - we use URIs instead of just IDs to identify objects uniquely in the frontend
    userUri = authState.user._meta.self
    
    // Let the API store work its magic. We just give it a URI, and it automagically retrieves the requested
    // data from the store (which acts as a cache), or from the API if it hasn't been fetched before.
    return apiStore.get(userUri)
  },
}

export default {
  state,
  mutations,
  getters,
}
