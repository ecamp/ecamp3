import { getters, loadFromLocalStorage, mutations } from '@/plugins/store/preferences'

let originalLocalStorage
beforeEach(() => {
  originalLocalStorage = window.localStorage
  window.localStorage = (() => {
    let store = {}

    return {
      getStore: () => store,
      getItem: (key) => store[key] ?? null,
      setItem: (key, value) => {
        store[key] = value?.toString() ?? 'undefined'
      },
      removeItem: (key) => {
        delete store[key]
      },
      clear: () => {
        store = {}
      },
      key: () => '',
      length: Object.keys(store).length,
    }
  })()
})

afterEach(() => {
  window.localStorage = originalLocalStorage
})

describe('reading state', () => {
  it('loads saved picasso edit mode true', async () => {
    // given
    const state = loadFromLocalStorage({
      'preferences:/camps/1a2b3c4d:picassoEditMode': 'true',
    })

    // when
    const result = getters.getPicassoEditMode(state)('/camps/1a2b3c4d')

    // then
    expect(result).toBeTruthy()
  })

  it('loads saved picasso edit mode false', () => {
    // given
    const state = loadFromLocalStorage({
      'preferences:/camps/1a2b3c4d:picassoEditMode': 'false',
    })

    // when
    const result = getters.getPicassoEditMode(state)('/camps/1a2b3c4d')

    // then
    expect(result).toBeFalsy()
  })

  it('falls back to false for picasso edit mode', () => {
    // given
    const state = loadFromLocalStorage({})

    // when
    const result = getters.getPicassoEditMode(state)('/camps/1a2b3c4d')

    // then
    expect(result).toBeFalsy()
  })

  it('handles invalid data for picasso edit mode', () => {
    // given
    const state = loadFromLocalStorage({
      'preferences:/camps/1a2b3c4d:picassoEditMode': 'invalid json',
    })

    // when
    const result = getters.getPicassoEditMode(state)('/camps/1a2b3c4d')

    // then
    expect(result).toBeFalsy()
  })

  it('loads saved story context edit mode true', () => {
    // given
    const state = loadFromLocalStorage({
      'preferences:/camps/1a2b3c4d:storyContextEditMode': 'true',
    })

    // when
    const result = getters.getStoryContextEditMode(state)('/camps/1a2b3c4d')

    // then
    expect(result).toBeTruthy()
  })

  it('loads saved story context edit mode false', () => {
    // given
    const state = loadFromLocalStorage({
      'preferences:/camps/1a2b3c4d:storyContextEditMode': 'false',
    })

    // when
    const result = getters.getStoryContextEditMode(state)('/camps/1a2b3c4d')

    // then
    expect(result).toBeFalsy()
  })

  it('falls back to false for story context edit mode', () => {
    // given
    const state = loadFromLocalStorage({})

    // when
    const result = getters.getStoryContextEditMode(state)('/camps/1a2b3c4d')

    // then
    expect(result).toBeFalsy()
  })

  it('handles invalid data for story context edit mode', () => {
    // given
    const state = loadFromLocalStorage({
      'preferences:/camps/1a2b3c4d:storyContextEditMode': 'invalid json',
    })

    // when
    const result = getters.getStoryContextEditMode(state)('/camps/1a2b3c4d')

    // then
    expect(result).toBeFalsy()
  })

  it('loads saved paper display size true', () => {
    // given
    const state = loadFromLocalStorage({
      'preferences:/camps/1a2b3c4d:paperDisplaySize': 'true',
    })

    // when
    const result = getters.getPaperDisplaySize(state)('/camps/1a2b3c4d')

    // then
    expect(result).toBeTruthy()
  })

  it('loads saved paper display size false', () => {
    // given
    const state = loadFromLocalStorage({
      'preferences:/camps/1a2b3c4d:paperDisplaySize': 'false',
    })

    // when
    const result = getters.getPaperDisplaySize(state)('/camps/1a2b3c4d')

    // then
    expect(result).toBeFalsy()
  })

  it('falls back to true for paper display size', () => {
    // given
    const state = loadFromLocalStorage({})

    // when
    const result = getters.getPaperDisplaySize(state)('/camps/1a2b3c4d')

    // then
    expect(result).toBeTruthy()
  })

  it('handles invalid data for paper display size', () => {
    // given
    const state = loadFromLocalStorage({
      'preferences:/camps/1a2b3c4d:paperDisplaySize': 'invalid json',
    })

    // when
    const result = getters.getPaperDisplaySize(state)('/camps/1a2b3c4d')

    // then
    expect(result).toBeTruthy()
  })

  it('loads saved print config undefined', () => {
    // given
    const state = loadFromLocalStorage({
      'preferences:/camps/1a2b3c4d:lastPrintConfig': 'undefined',
    })

    // when
    const result = getters.getLastPrintConfig(state)('/camps/1a2b3c4d')

    // then
    expect(result).toEqual({})
  })

  it('loads saved print config empty object', () => {
    // given
    const state = loadFromLocalStorage({
      'preferences:/camps/1a2b3c4d:lastPrintConfig': '{}',
    })

    // when
    const result = getters.getLastPrintConfig(state)('/camps/1a2b3c4d')

    // then
    expect(result).toEqual({})
  })

  it('loads saved print config with contents', () => {
    // given
    const state = loadFromLocalStorage({
      'preferences:/camps/1a2b3c4d:lastPrintConfig': '{"lang":"de"}',
    })

    // when
    const result = getters.getLastPrintConfig(state)('/camps/1a2b3c4d')

    // then
    expect(result).toEqual({
      lang: 'de',
    })
  })

  it('handles invalid data for print config', () => {
    // given
    const state = loadFromLocalStorage({
      'preferences:/camps/1a2b3c4d:lastPrintConfig': 'invalid json',
    })

    // when
    const result = getters.getLastPrintConfig(state)('/camps/1a2b3c4d')

    // then
    expect(result).toEqual({})
  })
})

describe('writing state', () => {
  it('saves picasso edit mode true', () => {
    // given
    const state = loadFromLocalStorage({
      'preferences:/camps/1a2b3c4d:picassoEditMode': 'false',
    })

    // when
    mutations.setPicassoEditMode(state, { campUri: '/camps/1a2b3c4d', editMode: true })

    // then
    expect(state.preferences['/camps/1a2b3c4d'].picassoEditMode).toBeTruthy()
    expect(
      window.localStorage.getItem('preferences:/camps/1a2b3c4d:picassoEditMode')
    ).toEqual('true')
  })

  it('saves picasso edit mode false', () => {
    // given
    const state = loadFromLocalStorage({
      'preferences:/camps/1a2b3c4d:picassoEditMode': 'true',
    })

    // when
    mutations.setPicassoEditMode(state, { campUri: '/camps/1a2b3c4d', editMode: false })

    // then
    expect(state.preferences['/camps/1a2b3c4d'].picassoEditMode).toBeFalsy()
    expect(
      window.localStorage.getItem('preferences:/camps/1a2b3c4d:picassoEditMode')
    ).toEqual('false')
  })

  it('saves picasso edit mode when previously not saved', () => {
    // given
    const state = loadFromLocalStorage({})

    // when
    mutations.setPicassoEditMode(state, { campUri: '/camps/1a2b3c4d', editMode: true })

    // then
    expect(state.preferences['/camps/1a2b3c4d'].picassoEditMode).toBeTruthy()
    expect(
      window.localStorage.getItem('preferences:/camps/1a2b3c4d:picassoEditMode')
    ).toEqual('true')
  })

  it('saves story context edit mode true', () => {
    // given
    const state = loadFromLocalStorage({
      'preferences:/camps/1a2b3c4d:storyContextEditMode': 'false',
    })

    // when
    mutations.setStoryContextEditMode(state, {
      campUri: '/camps/1a2b3c4d',
      editMode: true,
    })

    // then
    expect(state.preferences['/camps/1a2b3c4d'].storyContextEditMode).toBeTruthy()
    expect(
      window.localStorage.getItem('preferences:/camps/1a2b3c4d:storyContextEditMode')
    ).toEqual('true')
  })

  it('saves story context edit mode false', () => {
    // given
    const state = loadFromLocalStorage({
      'preferences:/camps/1a2b3c4d:storyContextEditMode': 'true',
    })

    // when
    mutations.setStoryContextEditMode(state, {
      campUri: '/camps/1a2b3c4d',
      editMode: false,
    })

    // then
    expect(state.preferences['/camps/1a2b3c4d'].storyContextEditMode).toBeFalsy()
    expect(
      window.localStorage.getItem('preferences:/camps/1a2b3c4d:storyContextEditMode')
    ).toEqual('false')
  })

  it('saves story context edit mode when previously not saved', () => {
    // given
    const state = loadFromLocalStorage({})

    // when
    mutations.setStoryContextEditMode(state, {
      campUri: '/camps/1a2b3c4d',
      editMode: true,
    })

    // then
    expect(state.preferences['/camps/1a2b3c4d'].storyContextEditMode).toBeTruthy()
    expect(
      window.localStorage.getItem('preferences:/camps/1a2b3c4d:storyContextEditMode')
    ).toEqual('true')
  })

  it('saves paper display size false', () => {
    // given
    const state = loadFromLocalStorage({
      'preferences:/camps/1a2b3c4d:paperDisplaySize': 'true',
    })

    // when
    mutations.setPaperDisplaySize(state, {
      campUri: '/camps/1a2b3c4d',
      paperDisplaySize: false,
    })

    // then
    expect(state.preferences['/camps/1a2b3c4d'].paperDisplaySize).toBeFalsy()
    expect(
      window.localStorage.getItem('preferences:/camps/1a2b3c4d:paperDisplaySize')
    ).toEqual('false')
  })

  it('saves paper display size true', () => {
    // given
    const state = loadFromLocalStorage({
      'preferences:/camps/1a2b3c4d:paperDisplaySize': 'false',
    })

    // when
    mutations.setPaperDisplaySize(state, {
      campUri: '/camps/1a2b3c4d',
      paperDisplaySize: true,
    })

    // then
    expect(state.preferences['/camps/1a2b3c4d'].paperDisplaySize).toBeTruthy()
    expect(
      window.localStorage.getItem('preferences:/camps/1a2b3c4d:paperDisplaySize')
    ).toEqual('true')
  })

  it('saves paper display size when previously not saved', () => {
    // given
    const state = loadFromLocalStorage({})

    // when
    mutations.setPaperDisplaySize(state, {
      campUri: '/camps/1a2b3c4d',
      paperDisplaySize: false,
    })

    // then
    expect(state.preferences['/camps/1a2b3c4d'].paperDisplaySize).toBeFalsy()
    expect(
      window.localStorage.getItem('preferences:/camps/1a2b3c4d:paperDisplaySize')
    ).toEqual('false')
  })

  it('saves print config with content', () => {
    // given
    const state = loadFromLocalStorage({
      'preferences:/camps/1a2b3c4d:lastPrintConfig': '{}',
    })

    // when
    mutations.setLastPrintConfig(state, {
      campUri: '/camps/1a2b3c4d',
      printConfig: { lang: 'de' },
    })

    // then
    expect(state.preferences['/camps/1a2b3c4d'].lastPrintConfig).toEqual({ lang: 'de' })
    expect(
      window.localStorage.getItem('preferences:/camps/1a2b3c4d:lastPrintConfig')
    ).toEqual('{"lang":"de"}')
  })

  it('saves print config undefined', () => {
    // given
    const state = loadFromLocalStorage({
      'preferences:/camps/1a2b3c4d:lastPrintConfig': '{}',
    })

    // when
    mutations.setLastPrintConfig(state, {
      campUri: '/camps/1a2b3c4d',
      printConfig: undefined,
    })

    // then
    expect(state.preferences['/camps/1a2b3c4d'].lastPrintConfig).toEqual(undefined)
    expect(
      window.localStorage.getItem('preferences:/camps/1a2b3c4d:lastPrintConfig')
    ).toEqual('undefined')
  })

  it('saves print config when previously not saved', () => {
    // given
    const state = loadFromLocalStorage({})

    // when
    mutations.setLastPrintConfig(state, {
      campUri: '/camps/1a2b3c4d',
      printConfig: { lang: 'de' },
    })

    // then
    expect(state.preferences['/camps/1a2b3c4d'].lastPrintConfig).toEqual({ lang: 'de' })
    expect(
      window.localStorage.getItem('preferences:/camps/1a2b3c4d:lastPrintConfig')
    ).toEqual('{"lang":"de"}')
  })
})
