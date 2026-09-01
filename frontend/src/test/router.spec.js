import { describe, it, expect, vi } from 'vitest'
import router, {
  activityFromRoute,
  campFromRoute,
  materialListRoute,
  scheduleEntryRoute,
} from '../router'

vi.mock('@/plugins/auth', () => ({
  isAdmin: () => false,
  isLoggedIn: () => true,
}))

vi.mock('@/plugins/store', () => ({
  apiStore: {
    get: () => ({
      activities: ({ id }) => {
        if (!id.startsWith('activity-')) return `activity ${id}`

        const activity = {
          scheduleEntries: () => ({
            items: [{ id: id.replace('activity-', 'entry-') }],
          }),
        }
        activity.$reload = () => Promise.resolve(activity)
        return activity
      },
      camps: ({ id }) =>
        id === 'camp-1' ? { _meta: { load: Promise.resolve() } } : `camp ${id}`,
      periods: ({ id }) => ({ id, _meta: { load: Promise.resolve() } }),
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

describe('activity back route', () => {
  it('keeps the original program route when switching activities', async () => {
    const activityRoute = (number) => {
      const activity = {
        id: `activity-${number}`,
        title: `Activity ${number}`,
        _meta: { loading: false },
        camp: () => ({ id: 'camp-1', shortTitle: 'camp' }),
      }
      return scheduleEntryRoute({
        id: `entry-${number}`,
        _meta: { loading: false },
        activity: () => activity,
      })
    }

    await router.replace({
      name: 'camp/period/program',
      params: { campId: 'camp-1', campShortTitle: 'camp', periodId: 'period-1' },
    })
    const origin = router.currentRoute.value.fullPath

    await router.push(activityRoute(1))
    const firstActivity = router.currentRoute.value.fullPath
    await router.push(activityRoute(2))

    expect(window.history.state.activityBack).toBe(origin)

    const navigatedBack = new Promise((resolve) => {
      const removeHook = router.afterEach(() => {
        removeHook()
        resolve()
      })
    })
    window.history.back()
    await navigatedBack

    expect(router.currentRoute.value.fullPath).toBe(firstActivity)
    expect(window.history.state.activityBack).toBe(origin)
  }, 15_000)
})
