import { describe, it, expect, vi } from 'vitest'
import { activityFromRoute, campFromRoute, materialListRoute } from '../router'

vi.mock('@/plugins/store', () => ({
  apiStore: {
    get: () => ({
      activities: ({ id }) => `activity ${id}`,
      camps: ({ id }) => `camp ${id}`,
    }),
  },
}))

describe('materialListRoute', () => {
  const camp = {
    id: '42',
    shortTitle: 'this is irrelevant',
    _meta: { loading: false },
  }

  it('returns empty object if camp is loading', () => {
    const loadingCamp = { ...camp, _meta: { loading: true } }
    expect(materialListRoute(loadingCamp)).toEqual({})
  })

  it('returns route for default string argument "/all"', () => {
    const result = materialListRoute(camp)
    expect(result).toEqual({
      name: 'camp/material/all',
      params: {
        campId: '42',
        campShortTitle: 'this-is-irreleva',
      },
      query: {},
    })
  })

  it('returns route for specific string argument "/unassigned"', () => {
    const result = materialListRoute(camp, '/unassigned')
    expect(result).toEqual({
      name: 'camp/material/unassigned',
      params: {
        campId: '42',
        campShortTitle: 'this-is-irreleva',
      },
      query: {},
    })
  })

  it('returns route for a material list object', () => {
    const materialList = {
      id: '42',
      name: 'this is irrelevant',
      _meta: { loading: false },
    }
    const result = materialListRoute(camp, materialList)
    expect(result).toEqual({
      name: 'camp/material/detail',
      params: {
        campId: '42',
        campShortTitle: 'this-is-irreleva',
        materialId: '42',
        materialName: 'this-is-irrelevant',
      },
      query: {},
    })
  })

  it('returns empty object if material list object is missing _meta', () => {
    const materialList = {
      id: '42',
      name: 'this is irrelevant',
    }
    expect(materialListRoute(camp, materialList)).toEqual({})
  })

  it('returns empty object if material list is loading', () => {
    const materialList = {
      id: '42',
      name: 'this is irrelevant',
      _meta: { loading: true },
    }
    expect(materialListRoute(camp, materialList)).toEqual({})
  })

  it('correctly includes query parameters for /all', () => {
    const query = { search: 'test' }
    const result = materialListRoute(camp, '/all', query)
    expect(result.query).toEqual(query)
  })

  it('correctly includes query parameters for materialList', () => {
    const query = { search: 'test' }
    const materialList = {
      id: '42',
      name: 'this is irrelevant',
      _meta: { loading: false },
    }
    const resultWithObject = materialListRoute(camp, materialList, query)
    expect(resultWithObject.query).toEqual(query)
  })
})

describe('campFromRoute', () => {
  it('returns the camp entity for the route parameter', () => {
    expect(campFromRoute({ params: { campId: '42' } })).toBe('camp 42')
  })

  it('returns undefined when the route has no camp', () => {
    expect(campFromRoute({ params: {} })).toBeUndefined()
  })
})

describe('activityFromRoute', () => {
  it('returns the activity entity for the route parameter', () => {
    expect(activityFromRoute({ params: { activityId: '42' } })).toBe('activity 42')
  })

  it('returns undefined when the route has no activity', () => {
    expect(activityFromRoute({ params: { campId: '42' } })).toBeUndefined()
  })

  it('leaves it to the API to reject a malformed activity id', () => {
    expect(() => activityFromRoute({ params: { activityId: 'nope' } })).not.toThrow()
  })
})
