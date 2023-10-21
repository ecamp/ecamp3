import HalJsonVuex from 'hal-json-vuex'
import axios from 'axios'
import { createStore } from 'vuex'

export default defineNuxtPlugin((nuxtApp) => {
  console.log('clear axios interceptors')
  axios.interceptors.request.clear()
  axios.interceptors.response.clear()

  const requestHeaders = useRequestHeaders(['cookie'])
  console.log(requestHeaders)

  const store = createStore({
    state() {
      return {}
    },
  })
  nuxtApp.vueApp.use(store)

  const runtimeConfig = useRuntimeConfig()

  if (runtimeConfig.BASIC_AUTH_TOKEN) {
    axios.interceptors.request.use(function (config) {
      if (!config.headers['Authorization']) {
        config.headers['Authorization'] = `Basic ${runtimeConfig.BASIC_AUTH_TOKEN}`
      }
      return config
    })
  }

  if (process.env.NODE_ENV !== 'production') {
    axios.interceptors.request.use(function (config) {
      config.meta = config.meta || {}
      config.meta.requestStartedAt = new Date().getTime()
      return config
    })

    axios.interceptors.response.use(
      function (response) {
        console.log(
          `${response.config.url} - execution time: ${
            new Date().getTime() - response.config.meta.requestStartedAt
          } ms`
        )
        return response
      },
      (error) => {
        console.log(error)
        console.log(`${error.config.url} - call to API failed`)
      }
    )
  }

  axios.defaults.withCredentials = true
  axios.defaults.baseURL = 'http://caddy:3000/api' // process.env.INTERNAL_API_ROOT_URL || 'http://caddy:3000/api'
  axios.defaults.headers.common.Accept = 'application/hal+json'
  axios.interceptors.request.use(function (config) {
    console.log('request interceptor')
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
