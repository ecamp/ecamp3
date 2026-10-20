import { createStore } from 'vuex'
import { createHalc, createFetchTransport } from 'halc'
import { hal } from 'halc/hal'
import { halcVue } from 'halc/vue'
import { createVuexStore } from 'halc/vuex'
import lang from './lang'
import auth from './auth'
import preferences from './preferences'
import { getEnv } from '@/environment.js'

export default {
  install: (app, _) => {
    store = createStore({
      modules: {
        lang,
        auth,
        preferences,
      },
      strict: false,
    })

    app.use(store)

    // create and inject API
    apiStore = createHalc({
      adapter: hal,
      baseUrl: getEnv().API_ROOT_URL,
      store: createVuexStore(store),
      transport: createFetchTransport({ credentials: 'include' }),
      forceRequestedSelfLink: true,
    })

    app.use(halcVue(apiStore))
  },
}

export let apiStore
export let store
