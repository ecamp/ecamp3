import { render } from '@/test/renderWithVuetify.js'
import Admin from '../Admin.vue'
import flushPromises from 'flush-promises'

describe('Admin view', () => {
  it('shows the danger zone when the user has a manager role', async () => {
    const { getByText } = render(Admin, {
      props: {
        camp: createCampWithRole('manager'),
      },
      routes: [],
      mocks: {
        $store: STORE,
        api: { reload: () => Promise.resolve() },
      },
      stubs: [
        'camp-settings',
        'camp-address',
        'camp-periods',
        'camp-categories',
        'camp-material-lists',
      ],
    })

    await flushPromises()

    expect(getByText('Gefahrenzone')).toBeInTheDocument()
    expect(getByText('Lager löschen')).toBeInTheDocument()
  })

  it("doesn't show the danger zone when the user has a member role", async () => {
    const { queryByText } = render(Admin, {
      props: {
        camp: createCampWithRole('member'),
      },
      routes: [],
      mocks: {
        $store: STORE,
        api: { reload: () => Promise.resolve() },
      },
      stubs: [
        'camp-settings',
        'camp-address',
        'camp-periods',
        'camp-categories',
        'camp-material-lists',
      ],
    })

    await flushPromises()

    expect(queryByText('Gefahrenzone')).not.toBeInTheDocument()
    expect(queryByText('Lager löschen')).not.toBeInTheDocument()
  })

  it("doesn't show the danger zone when the user has the guest role", async () => {
    const { queryByText } = render(Admin, {
      props: {
        camp: createCampWithRole('guest'),
      },
      routes: [],
      mocks: {
        $store: STORE,
        api: { reload: () => Promise.resolve() },
      },
      stubs: [
        'camp-settings',
        'camp-address',
        'camp-periods',
        'camp-categories',
        'camp-material-lists',
      ],
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

const STORE = {
  state: {
    auth: {
      user: USER,
    },
  },
}

function createCampWithRole(role) {
  return () => ({
    campCollaborations: () => ({
      items: [
        {
          role: role,
          user: () => USER,
        },
      ],
    }),
    materialLists: () => {},
    _meta: { load: Promise.resolve() },
  })
}
