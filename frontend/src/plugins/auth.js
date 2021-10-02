import axios from 'axios'
import { apiStore } from '@/plugins/store'
import router from '@/router'

axios.interceptors.response.use(null, error => {
  if (error.status === 401) {
    logout().then(() => {})
  }
  return Promise.reject(error)
})

function isLoggedIn () {
  return apiStore.get().authenticated
}

export async function refreshLoginStatus (forceReload = true) {
  if (forceReload) apiStore.reload(apiStore.get())
  await apiStore.get()._meta.load
  return isLoggedIn()
}

async function login (username, password) {
  const url = await apiStore.href(apiStore.get().auth(), 'login')
  return apiStore.post(url, { username: username, password: password }).then(() => refreshLoginStatus())
}

async function register (data) {
  const url = await apiStore.href(apiStore.get(), 'user')
  return apiStore.post(url, data)
}

async function redirectToOAuthLogin (provider) {
  let returnUrl = window.location.origin + router.resolve({ name: 'loginCallback' }).href

  const params = new URLSearchParams(window.location.search)
  if (params.has('redirect')) {
    returnUrl += '?redirect=' + params.get('redirect')
  }

  return apiStore.href(apiStore.get().auth(), provider, { callback: encodeURI(returnUrl) }).then(url => {
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
  return apiStore.reload(apiStore.get().auth().logout())
    .then(() => refreshLoginStatus())
    .then(() => router.push({ name: 'login' }))
    .then(() => apiStore.purgeAll())
}

export const auth = { isLoggedIn, refreshLoginStatus, login, register, loginGoogle, loginPbsMiData, logout }

class AuthPlugin {
  install (Vue) {
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
