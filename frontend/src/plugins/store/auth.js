import { apiStore } from '@/plugins/store/index'

/**
 * Because we cannot differentiate between a expired cookie and a deleted cookie,
 * we use localStorage to track if a user has logged out and does not want
 * to refresh the access token.
 */
const HAS_LOGGED_OUT = 'hasLoggedOut'

export const state = {
  user: null,
}

export const mutations = {
  login(state, user) {
    state.user = user
    window.localStorage.setItem(HAS_LOGGED_OUT, 'false')
  },

  logout(state) {
    state.user = null
    window.localStorage.setItem(HAS_LOGGED_OUT, 'true')
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

  hasLoggedOut() {
    return window.localStorage.getItem(HAS_LOGGED_OUT) === 'true'
  },
}

export default {
  state,
  mutations,
  getters,
}
