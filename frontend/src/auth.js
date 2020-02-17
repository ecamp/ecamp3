import Vue from 'vue'
import axios from 'axios'

const STORAGE_LOCATION = 'loggedIn'
const LOGGED_IN = '1'
const LOGGED_OUT = '0'

const subscribers = []

const notifySubscribers = newLoginStatus => {
  subscribers.forEach(subscriber => subscriber(newLoginStatus))
}

export const auth = {
  async isLoggedIn () {
    const savedStatus = window.localStorage.getItem(STORAGE_LOCATION)
    if (savedStatus !== null) {
      return savedStatus === LOGGED_IN
    }
    const response = await axios.get(process.env.VUE_APP_ROOT_API + '/login')
    let loggedIn = LOGGED_OUT
    if (response.data.user !== 'guest') {
      loggedIn = LOGGED_IN
    }
    window.localStorage.setItem(STORAGE_LOCATION, loggedIn)
    return loggedIn === LOGGED_IN
  },
  subscribe (onLoginStatusChange) {
    subscribers.push(onLoginStatusChange)
  },
  login (username, password) {
    return axios.post(process.env.VUE_APP_ROOT_API + '/login/login', { username: username, password: password })
      .then(resp => {
        if (resp.data.user !== 'guest') {
          this.loginSuccess()
          return true
        } else {
          return false
        }
      })
  },
  loginGoogle (returnUrl) {
    window.open(process.env.VUE_APP_ROOT_API + '/login/google?callback=' + encodeURI(returnUrl), '', 'width=500px,height=600px')
  },
  loginPbsMiData (returnUrl) {
    window.open(process.env.VUE_APP_ROOT_API + '/login/pbsmidata?callback=' + encodeURI(returnUrl), '', 'width=500px,height=600px')
  },
  loginSuccess () {
    window.localStorage.setItem(STORAGE_LOCATION, LOGGED_IN)
    notifySubscribers(true)
  },
  logout (callback) {
    axios.get(process.env.VUE_APP_ROOT_API + '/login/logout').then(response => {
      window.localStorage.setItem(STORAGE_LOCATION, LOGGED_OUT)
      notifySubscribers(false)
      if (callback) {
        callback(response)
      }
    })
  }
}

axios.interceptors.response.use(null, error => {
  if (error.status === 401) {
    auth.logout()
  }
  return Promise.reject(error)
})

Vue.auth = auth
Object.defineProperties(Vue.prototype, {
  $auth: {
    get () {
      return auth
    }
  }
})
