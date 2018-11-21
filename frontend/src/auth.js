import Vue from 'vue'
import axios from 'axios'
const storageLocation = 'loggedIn'

export const auth = {
  isLoggedIn () {
    let savedStatus = window.localStorage.getItem(storageLocation)
    if (savedStatus === null) {
      axios.get(process.env.VUE_APP_ROOT_API + '/login').then(response => {
        let loggedIn = '0'
        if (response.data.user !== 'guest') {
          loggedIn = '1'
        }
        window.localStorage.setItem(storageLocation, loggedIn)
      })
    }
    return window.localStorage.getItem(storageLocation) === '1'
  },
  login (returnUrl) {
    window.open(process.env.VUE_APP_ROOT_API + '/login/google?callback=' + encodeURI(returnUrl), '', 'width=500px,height=600px')
  },
  loginSuccess () {
    window.localStorage.setItem(storageLocation, '1')
    console.log('logged in')
  },
  logout (callback) {
    axios.get(process.env.VUE_APP_ROOT_API + '/logout').then(response => {
      window.localStorage.setItem(storageLocation, '0')
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
