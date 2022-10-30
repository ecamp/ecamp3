import { render } from '@testing-library/vue'
import Admin from '../Admin.vue'
import Vue from 'vue'
import Vuetify from 'vuetify'
import flushPromises from 'flush-promises'

Vue.use(Vuetify)

const renderWithVuetify = (component, options, callback) => {
  const root = document.createElement('div')
  root.setAttribute('data-app', 'true')

  return render(
    component,
    {
      container: document.body.appendChild(root),
      vuetify: new Vuetify(),
      ...options,
    },
    callback
  )
}

describe('Admin view', () => {
  it('shows the danger zone when the user has a manager role', async () => {
    const { getByText } = renderWithVuetify(Admin, {
      props: {
        camp: createCampWithRole('manager'),
      },
      routes: [],
      mocks: {
        $store: STORE,
        api: { reload: () => Promise.resolve() },
        $tc: (key) => key,
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

    expect(getByText('components.campAdmin.campDangerZone.title')).toBeInTheDocument()
    expect(
      getByText('components.campAdmin.campDangerZone.deleteCamp.title')
    ).toBeInTheDocument()
  })

  it("doesn't show the danger zone when the user has a member role", async () => {
    const { queryByText } = renderWithVuetify(Admin, {
      props: {
        camp: createCampWithRole('member'),
      },
      routes: [],
      mocks: {
        $store: STORE,
        api: { reload: () => Promise.resolve() },
        $tc: (key) => key,
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

    expect(
      queryByText('components.campAdmin.campDangerZone.title')
    ).not.toBeInTheDocument()
    expect(
      queryByText('components.campAdmin.campDangerZone.deleteCamp.title')
    ).not.toBeInTheDocument()
  })

  it("doesn't show the danger zone when the user has the guest role", async () => {
    const { queryByText } = renderWithVuetify(Admin, {
      props: {
        camp: createCampWithRole('guest'),
      },
      routes: [],
      mocks: {
        $store: STORE,
        api: { reload: () => Promise.resolve() },
        $tc: (key) => key,
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

    expect(
      queryByText('components.campAdmin.campDangerZone.title')
    ).not.toBeInTheDocument()
    expect(
      queryByText('components.campAdmin.campDangerZone.deleteCamp.title')
    ).not.toBeInTheDocument()
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
