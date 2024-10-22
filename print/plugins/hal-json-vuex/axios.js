export function addAuthorizationInterceptor(axios) {
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

export function addDebugInterceptor(axios) {
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

export function addErrorLogInterceptor(axios) {
  axios.interceptors.response.use(
    (response) => response,
    (error) => {
      console.error(`fetch from API failed: ${error.config.url}`)
      return Promise.reject(error)
    }
  )
}
