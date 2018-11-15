import Vue from 'vue'
import axios from 'axios'

const storageLocation = 'loggedInUserToken'

export const auth = {
  isLoggedIn () {
    return this.loggedInUserToken() != null
  },
  loggedInUserToken () {
    return window.localStorage.getItem(storageLocation)
  },
  loggedInUser () {
    return JSON.parse(atob(this.loggedInUserToken().split('.')[1]))
  },
  login (returnUrl) {
    window.open(process.env.VUE_APP_ROOT_API + '/login/google?callback=' + encodeURI(returnUrl), '', 'width=500px,height=600px')
  },
  loginSuccess (vm, jwt) {
    window.localStorage.setItem(storageLocation, jwt)
    this.setAuthorizationHeader()
    console.log(this.loggedInUser())
  },
  logout () {
    window.localStorage.removeItem(storageLocation)
    this.setAuthorizationHeader()
  },
  setAuthorizationHeader () {
    let value = this.loggedInUserToken()
    if (value !== null) {
      axios.defaults.headers.common['Authorization'] = 'Bearer ' + value
    } else {
      delete axios.defaults.headers.common['Authorization']
    }
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
