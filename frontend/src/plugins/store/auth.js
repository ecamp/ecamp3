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
   * @returns The latest Logged in User Object with a profile()
   */
  getLoggedInUserWithProfile: (authState, _getters, _rootState, rootGetters) => {
    if (!authState.user) return authState.user
    let userFromApiStoreById = rootGetters.getUserFromApiStoreById(authState.user.id)
    if (!userFromApiStoreById) return authState.user
    let profileHref =
      userFromApiStoreById._storeData?.profile?.href ?? userFromApiStoreById.profile.href
    let profile = () => apiStore.get(profileHref)
    return { ...userFromApiStoreById, profile }
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
