import { createStore, createLogger } from 'vuex'
import axios from 'axios'
import HalJsonVuex from 'hal-json-vuex'
import lang from './lang'
import auth from './auth'
import { getEnv } from '@/environment.js'

const debug = process.env.NODE_ENV !== 'production'

export default {
  install: (app, options) => {
    store = createStore({
      modules: {
        lang,
        auth,
      },
      strict: debug,
      plugins: debug ? [createLogger()] : [],
    })

    app.use(store)

    const axiosInstance = axios.create({
      withCredentials: true,
      baseURL: getEnv().API_ROOT_URL,
      headers: { common: { Accept: 'application/hal+json' } },
    })
    axiosInstance.interceptors.request.use(function (config) {
      if (config.method === 'patch') {
        config.headers['Content-Type'] = 'application/merge-patch+json'
      }
      return config
    })

    // create and inject API
    apiStore = new HalJsonVuex(store, axiosInstance, {
      forceRequestedSelfLink: true,
    })

    app.use(apiStore)
  },
}

export let apiStore
export let store
