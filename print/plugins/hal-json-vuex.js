import HalJsonVuex from 'hal-json-vuex'
import axios from 'axios'
import { createStore } from 'vuex'

export default defineNuxtPlugin((nuxtApp) => {
  // create store
  const store = createStore({
    state() {
      return {}
    },
  })
  nuxtApp.vueApp.use(store)

  // create axios instance
  const { internalApiRootUrl } = useRuntimeConfig()
  const axiosInstance = axios.create({
    withCredentials: true,
    baseURL: internalApiRootUrl,
    headers: { common: { Accept: 'application/hal+json' } },
  })
  addAuthorizationInterceptor(axiosInstance)
  addDebugInterceptor(axiosInstance)
  addErrorLogInterceptor(axiosInstance)

  // create and inject API
  const api = new HalJsonVuex(store, axiosInstance, {
    forceRequestedSelfLink: true,
  })

  return {
    provide: {
      api,
    },
  }
})

function addAuthorizationInterceptor(axios) {
  const { basicAuthToken } = useRuntimeConfig()
  const requestHeaders = useRequestHeaders(['cookie'])

  axios.interceptors.request.use(function (config) {
    if (
      config.baseURL &&
      new URL(config.baseURL).origin === new URL(config.url, config.baseURL).origin
    ) {
      // add cookie header, if origin is the same as baseUri
      config.headers['Cookie'] = requestHeaders.cookie
    } else {
      config.url = null // load baseUrl
    }

    // add basic auth header (e.g. for staging environment)
    if (basicAuthToken && !config.headers['Authorization']) {
      config.headers['Authorization'] = `Basic ${basicAuthToken}`
    }

    return config
  })
}

function addDebugInterceptor(axios) {
  if (!import.meta.env.DEV) {
    return
  }

  axios.interceptors.request.use(function (config) {
    console.log(`${config.url} - calling API...`)

    config.meta = config.meta || {}
    config.meta.requestStartedAt = new Date().getTime()
    return config
  })

  axios.interceptors.response.use(function (response) {
    console.log(
      `${response.config.url} - execution time: ${
        new Date().getTime() - response.config.meta.requestStartedAt
      } ms`
    )
    return response
  })
}

function addErrorLogInterceptor(axios) {
  axios.interceptors.response.use(
    (response) => response,
    (error) => {
      console.error(`fetch from API failed: ${error.config.url}`)
      return Promise.reject(error)
    }
  )
}
