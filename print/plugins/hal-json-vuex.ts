import HalJsonVuex from 'hal-json-vuex'
import axios from 'axios'
import { createStore } from 'vuex'
import {
  addAuthorizationInterceptor,
  addDebugInterceptor,
  addErrorLogInterceptor,
} from '~/plugins/hal-json-vuex/axios'
import type { RootEndpoint } from '~/common/helpers/endpoints'

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
  const api = HalJsonVuex<RootEndpoint, any>(store, axiosInstance, {
    forceRequestedSelfLink: true,
  })

  return {
    provide: {
      api,
    },
  }
})
