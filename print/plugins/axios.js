export default function ({ $axios, $config }) {
  if ($config.BASIC_AUTH_TOKEN) {
    $axios.onRequest((config) => {
      if (!config.headers['Authorization']) {
        config.headers['Authorization'] = `Basic ${$config.BASIC_AUTH_TOKEN}`
      }
      return config
    })
  }

  if (process.env.NODE_ENV !== 'production') {
    $axios.onRequest((config) => {
      config.meta = config.meta || {}
      config.meta.requestStartedAt = new Date().getTime()
      return config
    })

    $axios.onResponse((response) => {
      console.log(
        `${response.config.url} - execution time: ${
          new Date().getTime() - response.config.meta.requestStartedAt
        } ms`
      )
      return response
    })

    $axios.onError((error) => {
      console.log(`${error.config.url} - call to API failed`)
    })
  }
}
