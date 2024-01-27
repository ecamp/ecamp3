const KEY = 'preferences'

let initialValue
try {
  initialValue = JSON.parse(window.localStorage.getItem(KEY)) || {}
} catch (error) {
  initialValue = {}
}
export const state = {
  preferences: initialValue,
}

export const getters = {
  getPicassoEditMode: (state) => (campUri) => {
    return state.preferences[campUri]?.picassoEditMode ?? false
  },
  getStoryContextEditMode: (state) => (campUri) => {
    return state.preferences[campUri]?.storyContextEditMode ?? false
  },
  getLastPrintConfig:
    (state) =>
    (campUri, fallback = {}) => {
      return state.preferences[campUri]?.lastPrintConfig ?? fallback
    },
}

export const mutations = {
  /**
   * Changes the edit mode of the picasso (locked or unlocked)
   * @param state Vuex state
   * @param campUri The URI of the camp to which this preference belongs
   * @param editMode boolean value, true meaning unlocked, false meaning locked
   */
  setPicassoEditMode(state, { campUri, editMode }) {
    state.preferences[campUri] = state.preferences[campUri] || {}
    state.preferences[campUri].picassoEditMode = editMode
    window.localStorage.setItem(KEY, JSON.stringify(state.preferences))
  },
  /**
   * Changes the edit mode of the story context overview (locked or unlocked)
   * @param state Vuex state
   * @param campUri The URI of the camp to which this preference belongs
   * @param editMode boolean value, true meaning unlocked, false meaning locked
   */
  setStoryContextEditMode(state, { campUri, editMode }) {
    state.preferences[campUri] = state.preferences[campUri] || {}
    state.preferences[campUri].storyContextEditMode = editMode
    window.localStorage.setItem(KEY, JSON.stringify(state.preferences))
  },
  /**
   * Remembers the last used PDF print settings
   * @param state Vuex state
   * @param campUri The URI of the camp to which this preference belongs
   * @param printConfig an object describing the last used print configuration
   */
  setLastPrintConfig(state, { campUri, printConfig }) {
    state.preferences[campUri] = state.preferences[campUri] || {}
    state.preferences[campUri].lastPrintConfig = printConfig
    window.localStorage.setItem(KEY, JSON.stringify(state.preferences))
  },
}

export default {
  state,
  getters,
  mutations,
}
