import Vue from 'vue'

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
  getPaperDisplaySize: (state) => (campUri) => {
    return state.preferences[campUri]?.paperDisplaySize ?? true
  },
  getLastPrintConfig:
    (state) =>
    (campUri, fallback = {}) => {
      return state.preferences[campUri]?.lastPrintConfig ?? fallback
    },
}

function setPreferenceValue(state, campUri, key, value) {
  if (!(campUri in state.preferences)) Vue.set(state.preferences, campUri, {})
  Vue.set(state.preferences[campUri], key, value)
  window.localStorage.setItem(KEY, JSON.stringify(state.preferences))
}

export const mutations = {
  /**
   * Changes the edit mode of the picasso (locked or unlocked)
   * @param state Vuex state
   * @param campUri The URI of the camp to which this preference belongs
   * @param editMode boolean value, true meaning unlocked, false meaning locked
   */
  setPicassoEditMode(state, { campUri, editMode }) {
    setPreferenceValue(state, campUri, 'picassoEditMode', editMode)
  },
  /**
   * Changes the edit mode of the story context overview (locked or unlocked)
   * @param state Vuex state
   * @param campUri The URI of the camp to which this preference belongs
   * @param editMode boolean value, true meaning unlocked, false meaning locked
   */
  setStoryContextEditMode(state, { campUri, editMode }) {
    setPreferenceValue(state, campUri, 'storyContextEditMode', editMode)
  },
  /**
   * Changes the width of the displayed activity details between paper size and full-screen width
   * @param state Vuex state
   * @param campUri The URI of the camp to which this preference belongs
   * @param paperDisplaySize boolean value, true meaning unlocked, false meaning locked
   */
  setPaperDisplaySize(state, { campUri, paperDisplaySize }) {
    setPreferenceValue(state, campUri, 'paperDisplaySize', paperDisplaySize)
  },
  /**
   * Remembers the last used PDF print settings
   * @param state Vuex state
   * @param campUri The URI of the camp to which this preference belongs
   * @param printConfig an object describing the last used print configuration
   */
  setLastPrintConfig(state, { campUri, printConfig }) {
    setPreferenceValue(state, campUri, 'lastPrintConfig', printConfig)
  },
}

export default {
  state,
  getters,
  mutations,
}
