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
   * @returns The latest Logged-in User Object with a profile()
   */
  getLoggedInUserWithProfile: (_authState, getters) => {
    let loggedInUser = getters.getLoggedInUser
    if (typeof loggedInUser?.profile !== 'object') return loggedInUser
    if (!loggedInUser) return loggedInUser
    let profileHref =
      loggedInUser._storeData?.profile?.href ?? loggedInUser?.profile?.href
    let profile = () => apiStore.get(profileHref)
    return { ...loggedInUser, profile }
  },
  getLoggedInUser: (authState, _getters, _rootState, rootGetters) => {
    if (!authState.user) return authState.user
    let userFromApiStoreById = rootGetters.getUserFromApiStoreById(authState.user.id)
    return userFromApiStoreById ? userFromApiStoreById : authState.user
  },
}

export default {
  state,
  mutations,
  getters,
}
