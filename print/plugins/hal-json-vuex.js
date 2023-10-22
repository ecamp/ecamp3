import HalJsonVuex from 'hal-json-vuex'
import axios from 'axios'
import { createStore } from 'vuex'

export default defineNuxtPlugin((nuxtApp) => {
  const requestHeaders = useRequestHeaders(['cookie'])
  const { internalApiRootUrl } = useRuntimeConfig()

  const store = createStore({
    state() {
      return {}
    },
  })
  nuxtApp.vueApp.use(store)

  axios.defaults.withCredentials = true
  axios.defaults.baseURL = internalApiRootUrl
  axios.defaults.headers.common.Accept = 'application/hal+json'
  axios.interceptors.request.use(function (config) {
    if (config.method === 'patch') {
      config.headers['Content-Type'] = 'application/merge-patch+json'
    }
    config.headers['Cookie'] = requestHeaders.cookie
    return config
  })

  const api = new HalJsonVuex(store, axios, {
    forceRequestedSelfLink: true,
  })

  return {
    provide: {
      api,
    },
  }
})
