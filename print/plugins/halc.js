import { createHalc, createFetchTransport } from 'halc'
import { hal } from 'halc/hal'
import { halcVue } from 'halc/vue'

export default defineNuxtPlugin((nuxtApp) => {
  const { internalApiRootUrl } = useRuntimeConfig()

  const api = createHalc({
    adapter: hal,
    baseUrl: internalApiRootUrl,
    transport: loggingTransport(createFetchTransport({ credentials: 'include' })),
    interceptors: [authorizationInterceptor(internalApiRootUrl)],
    forceRequestedSelfLink: true,
  })
  nuxtApp.vueApp.use(halcVue(api))

  return {
    provide: {
      api,
    },
  }
})

function authorizationInterceptor(apiRootUrl) {
  const { basicAuthToken } = useRuntimeConfig()
  const requestHeaders = useRequestHeaders(['cookie'])

  return (request) => {
    const headers = { ...request.headers }
    let url = request.url
    if (new URL(apiRootUrl).origin === new URL(url, apiRootUrl).origin) {
      // add cookie header, if origin is the same as the API
      if (requestHeaders.cookie) headers['Cookie'] = requestHeaders.cookie
    } else {
      // never send requests (and cookies) to other origins
      url = apiRootUrl
    }

    // add basic auth header (e.g. for staging environment)
    if (basicAuthToken && !headers['Authorization']) {
      headers['Authorization'] = `Basic ${basicAuthToken}`
    }

    return { ...request, url, headers }
  }
}

function loggingTransport(transport) {
  return {
    async request(request) {
      const startedAt = Date.now()
      if (import.meta.env.DEV) console.log(`${request.url} - calling API...`)
      try {
        const response = await transport.request(request)
        if (import.meta.env.DEV) {
          console.log(`${request.url} - execution time: ${Date.now() - startedAt} ms`)
        }
        if (response.status >= 400) console.error(`fetch from API failed: ${request.url}`)
        return response
      } catch (error) {
        console.error(`fetch from API failed: ${request.url}`)
        throw error
      }
    },
  }
}
