import { apiStore } from '@/plugins/store/index'

/**
 * Because we cannot differentiate between a expired cookie and a deleted cookie,
 * we use localStorage to track if a user has logged out and does not want
 * to refresh the access token.
 */
const HAS_LOGGED_OUT = 'hasLoggedOut'
const REFRESH_TOKEN_EXPIRES_AT_KEY = 'refreshTokenExpiresAt'

export const state = {
  user: null,
  authInitializing: true,
  authRequired: false,
  refreshTokenExpiresAt: parseInt(
    window.localStorage.getItem(REFRESH_TOKEN_EXPIRES_AT_KEY) ?? '0',
    10
  ),
}

export const mutations = {
  login(state, user) {
    state.user = user
    window.localStorage.setItem(HAS_LOGGED_OUT, 'false')
  },

  logout(state) {
    state.user = null
    state.refreshTokenExpiresAt = 0
    window.localStorage.setItem(HAS_LOGGED_OUT, 'true')
    window.localStorage.removeItem(REFRESH_TOKEN_EXPIRES_AT_KEY)
  },

  setAuthInitializing(state, initializing) {
    state.authInitializing = initializing
  },

  setAuthRequired(state, required) {
    state.authRequired = required
  },

  setRefreshTokenExpiresAt(state, timestamp) {
    state.refreshTokenExpiresAt = timestamp
    window.localStorage.setItem(REFRESH_TOKEN_EXPIRES_AT_KEY, timestamp.toString())
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
  isAuthInitializing: (authState) => {
    return authState.authInitializing
  },
  isAuthRequired: (authState) => {
    return authState.authRequired
  },
}

export function hasLoggedOutFromLocalStorage() {
  return window.localStorage.getItem(HAS_LOGGED_OUT) === 'true'
}

export function getRefreshTokenExpiresAt() {
  const stored = window.localStorage.getItem(REFRESH_TOKEN_EXPIRES_AT_KEY)
  return stored ? parseInt(stored, 10) : 0
}

export default {
  state,
  mutations,
  getters,
}
