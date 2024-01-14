import HalJsonVuex from 'hal-json-vuex'
import axios from 'axios'
import { createStore } from 'vuex'
import { getEnv } from '@/environment.js'

export default {
  install: (app, options) => {
    // create store
    const store = createStore({
      state() {
        return {}
      },
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
    const api = new HalJsonVuex(store, axiosInstance, {
      forceRequestedSelfLink: true,
    })

    app.use(api)
  },
}
