import { describe, expect, it } from 'vitest'
import Vue from 'vue'
import { fireEvent } from '@testing-library/vue'
import { render } from '@/test/renderWithVuetify.js'
import Admin from '../Info.vue'
import flushPromises from 'flush-promises'

describe('Admin view', () => {
  it('shows the danger zone when the user has a manager role', async () => {
    const camp = createCampWithRole('manager')
    const { getByText } = render(Admin, {
      props: {
        camp,
      },
      routes: [],
      store: createStoreObject(),
      mocks: {
        api: createApiMock(camp),
      },
      stubs: createStubs(),
    })

    await flushPromises()

    const dangerZone = getByText('Gefahrenzone')

    expect(dangerZone).toBeInTheDocument()
    await fireEvent.click(dangerZone)
    await Vue.nextTick()
    expect(getByText('Lager löschen')).toBeInTheDocument()
  })

  it("doesn't show the danger zone when the user has a member role", async () => {
    const camp = createCampWithRole('member')
    const { queryByText } = render(Admin, {
      props: {
        camp,
      },
      routes: [],
      store: createStoreObject(),
      mocks: {
        api: createApiMock(camp),
      },
      stubs: createStubs(),
    })

    await flushPromises()

    expect(queryByText('Gefahrenzone')).not.toBeInTheDocument()
    expect(queryByText('Lager löschen')).not.toBeInTheDocument()
  })

  it("doesn't show the danger zone when the user has the guest role", async () => {
    const camp = createCampWithRole('guest')
    const { queryByText } = render(Admin, {
      props: {
        camp,
      },
      routes: [],
      store: createStoreObject(),
      mocks: {
        api: createApiMock(camp),
      },
      stubs: createStubs(),
    })

    await flushPromises()

    expect(queryByText('Gefahrenzone')).not.toBeInTheDocument()
    expect(queryByText('Lager löschen')).not.toBeInTheDocument()
  })
})

const USER_URL = '/users/17d341a80579'

const USER = {
  _meta: {
    self: USER_URL,
  },
}

function createStoreObject() {
  return {
    state: {
      auth: {
        user: USER,
      },
    },
    getters: {
      getLoggedInUser: (state) => {
        return state.auth.user
      },
    },
  }
}

function createCampWithRole(role) {
  return {
    campCollaborations: () => ({
      items: [
        {
          role: role,
          user: () => USER,
          _meta: {
            loading: false,
          },
        },
      ],
    }),
    materialLists: () => {},
    progressLabels: () => {},
    _meta: { load: Promise.resolve() },
  }
}

function createApiMock(camp) {
  const halJsonResponse = {
    ...camp,
    _meta: {
      loading: false,
      load: Promise.resolve(camp),
    },
  }
  return {
    reload: () => Promise.resolve(),
    get: () => halJsonResponse,
  }
}

function createStubs() {
  return [
    'CampSettings',
    'CampAddress',
    'CampPeriods',
    'CampCategories',
    'CampActivityProgressLabels',
    'CampMaterialLists',
    'CampConditionalFields',
  ]
}
