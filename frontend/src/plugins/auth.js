import axios from 'axios'
import { get, reload, post, href } from '@/plugins/store'
import router from '@/router'

axios.interceptors.response.use(null, error => {
  if (error.status === 401) {
    logout()
  }
  return Promise.reject(error)
})

function isLoggedIn () {
  return get().auth().role === 'user'
}

export async function refreshLoginStatus (forceReload = true) {
  if (forceReload) reload(get().auth())
  await get().auth()._meta.load
  return isLoggedIn()
}

async function login (username, password) {
  const url = await href(get().auth(), 'login')
  return post(url, { username: username, password: password }).then(() => refreshLoginStatus())
}

async function register ({ username, email, password }) {
  const url = await href(get().auth(), 'register')
  return post(url, { username, email, password })
}

async function oAuthLoginInSeparateWindow (provider) {
  return new Promise(resolve => {
    // Make the promise resolve function available on global level, so the separate window can call it
    window.afterLogin = resolve

    href(get().auth(), provider).then(url => {
      const returnUrl = window.location.origin + router.resolve({ name: 'loginCallback' }).href
      // TODO use templated relations once #369 is implemented
      window.open(url + '?callback=' + encodeURI(returnUrl), '', 'width=500px,height=600px')
    })
  })
}

async function loginGoogle () {
  return oAuthLoginInSeparateWindow('google').then(() => refreshLoginStatus())
}

async function loginPbsMiData () {
  return oAuthLoginInSeparateWindow('pbsmidata').then(() => refreshLoginStatus())
}

async function logout () {
  get().auth().logout()
  location.reload() // TODO: Should be implemented in the store, for mobile use
}

export const auth = { isLoggedIn, refreshLoginStatus, login, register, loginGoogle, loginPbsMiData, logout }

class AuthPlugin {
  install (Vue, options) {
    Object.defineProperties(Vue.prototype, {
      $auth: {
        get () {
          return auth
        }
      }
    })
  }
}

export default new AuthPlugin()
