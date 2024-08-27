import Vue from 'vue'

const LOCAL_STORAGE_PREFIX = 'preferences:'

export function loadFromLocalStorage(localStorage = window.localStorage) {
  const values = {}
  Object.keys(localStorage)
    .filter((key) => key.startsWith(LOCAL_STORAGE_PREFIX))
    .forEach((key) => {
      try {
        const [, uri, identifier] = key.match(
          new RegExp(`^${LOCAL_STORAGE_PREFIX}(.*):(.*)$`)
        )
        values[uri] ||= {}
        values[uri][identifier] = JSON.parse(localStorage[key])
      } catch {
        // Just ignore this key and continue with the others
      }
    })
  return {
    preferences: values,
  }
}

export const state = loadFromLocalStorage()

export const getters = {
  getPicassoEditMode: (state) => (campUri) => {
    return !!(state.preferences[campUri]?.picassoEditMode ?? false)
  },
  getStoryContextEditMode: (state) => (campUri) => {
    return !!(state.preferences[campUri]?.storyContextEditMode ?? false)
  },
  getPaperDisplaySize: (state) => (campUri) => {
    return !!(state.preferences[campUri]?.paperDisplaySize ?? true)
  },
  getLastPrintConfig:
    (state) =>
    (campUri, fallback = {}) => {
      return state.preferences[campUri]?.lastPrintConfig ?? fallback
    },
}

function setPreferenceValue(state, campUri, identifier, value) {
  if (!(campUri in state.preferences)) Vue.set(state.preferences, campUri, {})
  Vue.set(state.preferences[campUri], identifier, value)
  window.localStorage.setItem(
    `${LOCAL_STORAGE_PREFIX}${campUri}:${identifier}`,
    JSON.stringify(value)
  )
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
