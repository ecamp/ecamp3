import Dashboard from '../Dashboard.vue'
import { shallowMount } from '@vue/test-utils'

describe('Dashboard view', () => {
  it('Renders View', async () => {
    const vueWrapper = shallowMount(Dashboard, DEFAULT_DASHBOARD_OPTIONS())

    expect(vueWrapper.html()).toBeTruthy()
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
    $tc: () => '',
    api: { reload: () => Promise.resolve() },
  },
  data: () => ({
    loading: false,
    filter: {
      period: null,
      responsible: [],
      category: [],
    },
  }),
  computed: {
    periods: () => {},
    progressLabels: () => {},
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
