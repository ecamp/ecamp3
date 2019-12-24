import Vue from 'vue'
import axios from 'axios'
const storageLocation = 'loggedIn'

const subscribers = []

const notifySubscribers = newLoginStatus => {
  subscribers.forEach(subscriber => subscriber(newLoginStatus))
}

export const auth = {
  async isLoggedIn () {
    const savedStatus = window.localStorage.getItem(storageLocation)
    if (savedStatus !== null) {
      return savedStatus === '1'
    }
    const response = await axios.get(process.env.VUE_APP_ROOT_API + '/login')
    let loggedIn = '0'
    if (response.data.user !== 'guest') {
      loggedIn = '1'
    }
    window.localStorage.setItem(storageLocation, loggedIn)
    return loggedIn === '1'
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
  loginMiData (returnUrl) {
    window.open(process.env.VUE_APP_ROOT_API + '/login/midata?callback=' + encodeURI(returnUrl), '', 'width=500px,height=600px')
  },
  loginSuccess () {
    window.localStorage.setItem(storageLocation, '1')
    notifySubscribers(true)
  },
  logout (callback) {
    axios.get(process.env.VUE_APP_ROOT_API + '/login/logout').then(response => {
      window.localStorage.setItem(storageLocation, '0')
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
