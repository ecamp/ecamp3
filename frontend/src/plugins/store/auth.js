export const state = {
  user: null,
  token: null,
}

export const mutations = {
  login(state, user) {
    state.user = user
  },

  setToken(state, token) {
    state.token = token
  },

  logout(state) {
    state.user = null
    state.token = null
  },
}

export default {
  state,
  mutations,
}
