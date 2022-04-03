import axios from 'axios'
import { apiStore } from '@/plugins/store'
import router from '@/router'
import Cookies from 'js-cookie'

axios.interceptors.response.use(null, error => {
  if (error.status === 401) {
    logout().then(() => {})
  }
  return Promise.reject(error)
})

function getJWTPayloadFromCookie () {
  const jwtHeaderAndPayload = Cookies.get('jwt_hp')
  if (!jwtHeaderAndPayload) return ''

  return jwtHeaderAndPayload.split('.')[1]
}

function parseJWTPayload (payload) {
  if (!payload) return {}
  const base64 = payload.replace(/-/g, '+').replace(/_/g, '/')
  const jsonPayload = decodeURIComponent(atob(base64).split('').map(function (c) {
    return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)
  }).join(''))

  return JSON.parse(jsonPayload)
}

function getJWTExpirationTimestamp () {
  return (parseJWTPayload(getJWTPayloadFromCookie()).exp ?? 0) * 1000
}

export function isLoggedIn () {
  return Date.now() < getJWTExpirationTimestamp()
}

async function login (username, password) {
  const url = await apiStore.href(apiStore.get(), 'login')
  return apiStore.post(url, { username: username, password: password }).then(() => {
    return isLoggedIn()
  })
}

async function resetPasswordRequest (email, token) {
  const url = await apiStore.href(apiStore.get(), 'resetPassword')
  return apiStore.post(url, { email: email, token: token })
}

async function resetPassword (id, password, token) {
  const url = await apiStore.href(apiStore.get(), 'resetPassword', { id: id })
  return apiStore.patch(url, { password: password, token: token })
}

function user () {
  if (!getJWTPayloadFromCookie()) {
    return null
  }
  const user = apiStore.get(parseJWTPayload(getJWTPayloadFromCookie()).user)
  user._meta.load = user._meta.load.catch(e => {
    if (e.response && [401, 403, 404].includes(e.response.status)) {
      // 401 means no complete token was submitted, so we may be missing the JWT signature cookie
      // 403 means we can theoretically interact in some way with the user, but apparently not read it
      // 404 means the user doesn't exist or we don't have access to it
      // Either way, we aren't allowed to access the user from the token, so it's best to ask the user
      // to log in again.
      auth.logout()
      return
    }
    throw e
  })
  return user
}

async function register (data) {
  const url = await apiStore.href(apiStore.get(), 'users')
  return apiStore.post(url, data)
}

async function redirectToOAuthLogin (provider) {
  let returnUrl = window.location.origin + router.resolve({ name: 'loginCallback' }).href

  const params = new URLSearchParams(window.location.search)
  if (params.has('redirect')) {
    returnUrl += '?redirect=' + params.get('redirect')
  }

  return apiStore.href(apiStore.get(), provider, { callback: encodeURI(returnUrl) }).then(url => {
    window.location.href = window.environment.API_ROOT_URL + url
  })
}

async function loginGoogle () {
  return redirectToOAuthLogin('oauthGoogle')
}

async function loginPbsMiData () {
  return redirectToOAuthLogin('oauthPbsmidata')
}

async function loginCeviDB () {
  return redirectToOAuthLogin('oauthCevidb')
}

export async function logout () {
  Cookies.remove('jwt_hp', { domain: window.environment.SHARED_COOKIE_DOMAIN })
  return router.push({ name: 'login' })
    .then(() => apiStore.purgeAll())
    .then(() => isLoggedIn())
}

export const auth = {
  isLoggedIn,
  login,
  register,
  loginGoogle,
  loginPbsMiData,
  loginCeviDB,
  logout,
  user,
  resetPasswordRequest,
  resetPassword
}

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
