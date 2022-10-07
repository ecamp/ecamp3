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

export default {
  state,
  mutations,
}
