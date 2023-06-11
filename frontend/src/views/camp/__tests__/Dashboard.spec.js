import Dashboard from '../Dashboard.vue'
import { shallowMount } from '@vue/test-utils'
import flushPromises from 'flush-promises'

describe('Dashboard view', () => {
  it('Renders View', async () => {
    const vueWrapper = shallowMount(Dashboard, DEFAULT_DASHBOARD_OPTIONS())

    expect(vueWrapper.html()).toBeTruthy()
  })
  it('Loads URL Query into filter', async () => {
    const query = {
      responsible: ['fe6557a4b89f'],
      category: ['505e3fdf9e90', 'a47a60594096'],
      period: '16b2fcffdd8e',
    }
    const vueWrapper = shallowMount(Dashboard, {
      ...DEFAULT_DASHBOARD_OPTIONS(),
      mocks: { ...DEFAULT_DASHBOARD_OPTIONS().mocks, $route: { query } },
    })

    const expectedFilterValues = {
      period: '/periods/16b2fcffdd8e',
      responsible: ['/camp_collaborations/fe6557a4b89f'],
      category: ['/categories/505e3fdf9e90', '/categories/a47a60594096'],
    }
    await flushPromises()

    expect(vueWrapper.vm.$data.filter.url).toMatchObject(expectedFilterValues)
  })
  it('Parses filter value into URL', async () => {
    const filter = {
      period: '/periods/16b2fcffdd8e',
      responsible: ['/camp_collaborations/fe6557a4b89f'],
      category: ['/categories/505e3fdf9e90', '/categories/a47a60594096'],
    }
    const data = () => ({
      loggedInUser: USER,
      loading: false,
      filter,
    })
    const options = { ...DEFAULT_DASHBOARD_OPTIONS(), data }
    const wrapper = shallowMount(Dashboard, options)

    await flushPromises()
    wrapper.vm.persistRouterState()

    const expectedURLParams = {
      responsible: ['fe6557a4b89f'],
      category: ['505e3fdf9e90', 'a47a60594096'],
      period: '16b2fcffdd8e',
    }
    expect(options.mocks.$router.replace).toHaveBeenCalled()
    expect(options.mocks.$router.replace).toHaveBeenLastCalledWith({
      append: true,
      query: expectedURLParams,
    })
  })
})

const CAMP_COLLAB = '/camp_collaborations/58dc1b96dcce'
const USER_URL = '/users/17d341a80579'
const USER = {
  _meta: {
    self: USER_URL,
  },
}

const STORE = {
  state: {
    auth: {
      user: USER,
    },
  },
}

const ROUTE = () => ({
  query: {},
})
const ROUTER = () => ({
  replace: jest.fn(),
})
const AUTH = {
  loadUser: jest.fn(),
}

function createCampWithRole(role) {
  return () => ({
    campCollaborations: () => ({
      items: [
        {
          role: role,
          user: () => USER,
          _meta: { self: '/camp_collaborations/58dc1b96dcce' },
        },
      ],
    }),
  })
}

const DEFAULT_DASHBOARD_OPTIONS = () => ({
  propsData: {
    camp: createCampWithRole('manager'),
  },
  mocks: {
    $store: STORE,
    $auth: AUTH,
    $route: ROUTE(),
    $router: ROUTER(),
    $tc: () => {},
    api: { reload: () => Promise.resolve() },
  },
  data: () => ({
    loggedInUser: USER,
    loading: false,
    filter: {
      period: null,
      responsible: [],
      category: [],
    },
  }),
  computed: {
    periods: () => {},
    multiplePeriods: () => false,
    campCollaborations: () => {},
    categories: () => {},
    scheduleEntries: () => [],
    scheduleEntriesLoading: () => false,
    days: () => {},
    filteredScheduleEntries: () => [],
    groupedScheduleEntries: () => {},
    showOnlyMyActivities: () => false,
    loggedInCampCollaboration: () => CAMP_COLLAB,
    syncUrlQueryActive: () => true,
  },
  stubs: [
    'TextAlignBaseline',
    'FilterDivider',
    'ActivityRow',
    'SelectFilter',
    'BooleanFilter',
    'CategoryChip',
    'ContentCard',
    'UserAvatar',
  ],
})
