import Vue from 'vue'

export const state = {}

export const mutations = {
  /**
   * Adds a placeholder into the store that indicates that the entity with the given URI is currently being
   * fetched from the API and not yet available.
   * @param state Vuex state
   * @param uri   URI of the object that is being fetched
   */
  addEmpty (state, uri) {
    Vue.set(state, uri, { _meta: { self: uri, loading: true } })
  },
  /**
   * Adds entities loaded from the API to the Vuex store.
   * @param state Vuex state
   * @param data  An object mapping URIs to entities that should be merged into the Vuex state.
   */
  add (state, data) {
    Object.keys(data).forEach(uri => {
      Vue.set(state, uri, data[uri])
    })
  },
  /**
   * Removes a single entity from the Vuex store.
   * @param state Vuex state
   * @param uri   URI of the entity to be removed
   */
  purge (state, uri) {
    Vue.delete(state, uri)
  },
  /**
   * Removes a single entity from the Vuex store.
   * @param state Vuex state
   * @param uri   URI of the entity to be removed
   */
  purgeAll (state, uri) {
    Object.keys(state).forEach(uri => {
      Vue.delete(state, uri)
    })
  },
  /**
   * Marks a single entity in the Vuex store as deleting, meaning the process of deletion is currently ongoing.
   * @param state Vuex state
   * @param uri   URI of the entity that is currently being deleted
   */
  deleting (state, uri) {
    if (state[uri]) Vue.set(state[uri]._meta, 'deleting', true)
  },
  /**
   * Marks a single entity in the Vuex store as normal again, after it has been marked as deleting before.
   * @param state Vuex state
   * @param uri   URI of the entity that failed to be deleted
   */
  deletingFailed (state, uri) {
    if (state[uri]) Vue.set(state[uri]._meta, 'deleting', false)
  }
}

export default {
  state,
  mutations
}
