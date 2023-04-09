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
  /**
   * Since store.auth.user isn't always up to date - uses the logged-in user URI and returns the latest data for that user
   * @returns {*} the Logged-in user with the latest fetched api data
   */
  getLoggedInUser: (authState) => {
    return authState.user ? apiStore.get(authState.user._meta.self) : authState.user
  },
}

export default {
  state,
  mutations,
  getters,
}
