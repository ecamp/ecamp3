import Vue from 'vue'

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
  login () {
    window.open(process.env.VUE_APP_ROOT_API + '/login/google?redirect=' + encodeURI(window.location.href), '', 'width=500px,height=600px')
  },
  loginSuccess (vm, jwt) {
    window.localStorage.setItem(storageLocation, jwt)
    console.log(this.loggedInUser())
  },
  logout () {
    window.localStorage.removeItem(storageLocation)
  }
}

Vue.auth = auth
Object.defineProperties(Vue.prototype, {
  $auth: {
    get () {
      return auth
    }
  }
})
