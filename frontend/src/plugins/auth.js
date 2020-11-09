import axios from 'axios'
import { get, reload, post, href, purgeAll } from '@/plugins/store'
import router from '@/router'

axios.interceptors.response.use(null, error => {
  if (error.status === 401) {
    logout()
  }
  return Promise.reject(error)
})

function isLoggedIn () {
  return get().authenticated
}

export async function refreshLoginStatus (forceReload = true) {
  if (forceReload) reload(get())
  await get()._meta.load
  return isLoggedIn()
}

async function login (username, password) {
  const url = await href(get().auth(), 'login')
  return post(url, { username: username, password: password }).then(() => refreshLoginStatus())
}

async function register (data) {
  const url = await href(get().auth(), 'register')
  return post(url, data)
}

async function redirectToOAuthLogin (provider) {
  let returnUrl = window.location.origin + router.resolve({ name: 'loginCallback' }).href

  const params = new URLSearchParams(window.location.search)
  if (params.has('redirect')) {
    returnUrl += '?redirect=' + params.get('redirect')
  }

  return href(get().auth(), provider, { callback: encodeURI(returnUrl) }).then(url => {
    window.location.href = url
  })
}

async function loginGoogle () {
  return redirectToOAuthLogin('google')
}

async function loginPbsMiData () {
  return redirectToOAuthLogin('pbsmidata')
}

export async function logout () {
  return reload(get().auth().logout())
    .then(() => refreshLoginStatus())
    .then(() => router.push({ name: 'login' }))
    .then(() => purgeAll())
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
