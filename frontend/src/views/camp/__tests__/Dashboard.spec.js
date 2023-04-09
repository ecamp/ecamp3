import Dashboard from '../Dashboard.vue'
import { shallowMount  } from '@vue/test-utils'
import { groupBy, keyBy, mapValues } from 'lodash'

describe('Dashboard view', () => {
  it('Renders View', async () => {
    const vueWrapper = shallowMount (Dashboard, {
      propsData: {
        camp: createCampWithRole('manager'),
      },
      mocks: {
        $store: STORE,
        $auth: AUTH,
        $route: ROUTE,
        $router: ROUTER,
        $tc: ()=>{},
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
        campCollaborations: ()=> {},
        categories: ()=> {},
        scheduleEntries:()=> [],
        scheduleEntriesLoading: () => false,
        days: ()=> {},
        filteredScheduleEntries: ()=> [],
        groupedScheduleEntries: ()=> {},
        showOnlyMyActivities: () => false,
        loggedInCampCollaboration: () => CAMP_COLLAB
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

    expect(vueWrapper.html()).toBeTruthy();

  })
})
const CAMP_COLLAB =  '/camp_collaborations/58dc1b96dcce'
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

const ROUTE = {
  query: {},
}
const ROUTER = {
  replace: jest.fn(),
}
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
    materialLists: () => {},
    _meta: { load: Promise.resolve() },
    periods: () => ({
      _meta: { load: Promise.resolve() },
      items: [
        {
          _meta: { self: '/periods/071ca08f6d70', load: Promise.resolve() },
          scheduleEntries: EMPTY_ITEM_LIST('/schedule_entries/c3d36cd00544'),
          days: EMPTY_ITEM_LIST,
        },
        {
          _meta: { self: '/periods/16b2fcffdd8e', load: Promise.resolve() },
          scheduleEntries: EMPTY_ITEM_LIST('/schedule_entries/4bc1873a73f2'),
          days: EMPTY_ITEM_LIST,
        },
      ],
    }),
    activities: () => ({ _meta: { load: Promise.resolve() } }),
    categories: () => ({
      _meta: { load: Promise.resolve() },
      items: [
        { _meta: { self: '/categories/505e3fdf9e90' } },
        { _meta: { self: '/categories/6adced5270de' } },
        { _meta: { self: '/categories/9af703a10a9c' } },
        { _meta: { self: '/categories/a47a60594096' } },
      ],
    }),
  })
}
const EMPTY_ITEM_LIST = (self) => {
  return () => ({
    _meta: { loading: false, self },
    items: [],
  })
}
