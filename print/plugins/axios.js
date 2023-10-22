import axios from 'axios'

export default defineNuxtPlugin(() => {
  axios.interceptors.request.clear()
  axios.interceptors.response.clear()

  const { basicAuthToken } = useRuntimeConfig()

  if (basicAuthToken) {
    axios.interceptors.request.use(function (config) {
      if (!config.headers['Authorization']) {
        config.headers['Authorization'] = `Basic ${basicAuthToken}`
      }
      return config
    })
  }

  if (import.meta.env.DEV) {
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
        console.log(`${error.config.url} - call to API failed`)
      }
    )
  }
})
