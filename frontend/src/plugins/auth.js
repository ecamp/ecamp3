import axios from 'axios'
import { apiStore, store } from '@/plugins/store'
import router from '@/router'
import Cookies from 'js-cookie'

axios.interceptors.response.use(null, (error) => {
  if (error.status === 401) {
    logout().then(() => {})
  }
  return Promise.reject(error)
})

function getJWTPayloadFromCookie() {
  const jwtHeaderAndPayload = Cookies.get(headerAndPayloadCookieName())
  if (!jwtHeaderAndPayload) return ''

  return jwtHeaderAndPayload.split('.')[1]
}

function parseJWTPayload(payload) {
  if (!payload) return {}
  const base64 = payload.replace(/-/g, '+').replace(/_/g, '/')
  const jsonPayload = decodeURIComponent(
    atob(base64)
      .split('')
      .map(function (c) {
        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)
      })
      .join('')
  )

  return JSON.parse(jsonPayload)
}

function getJWTExpirationTimestamp() {
  return (parseJWTPayload(getJWTPayloadFromCookie()).exp ?? 0) * 1000
}

export function isLoggedIn() {
  const isLoggedIn = Date.now() < getJWTExpirationTimestamp()

  if (isLoggedIn) {
    loadUser()
  }

  return isLoggedIn
}

export function isAdmin() {
  if (!isLoggedIn()) {
    return false
  }

  return parseJWTPayload(getJWTPayloadFromCookie()).roles.includes('ROLE_ADMIN')
}

async function login(email, password) {
  const url = await apiStore.href(apiStore.get(), 'login')
  return apiStore.post(url, { identifier: email, password: password }).then(() => {
    return isLoggedIn()
  })
}

async function resetPasswordRequest(email, recaptchaToken) {
  const url = await apiStore.href(apiStore.get(), 'resetPassword')
  return apiStore.post(url, { email: email, recaptchaToken: recaptchaToken })
}

async function resetPassword(id, password, recaptchaToken) {
  const url = await apiStore.href(apiStore.get(), 'resetPassword', { id: id })
  return apiStore.patch(url, { password: password, recaptchaToken: recaptchaToken })
}

async function loadUser() {
  if (!getJWTPayloadFromCookie()) {
    store.commit('logout')
    return null
  }

  try {
    const user = await apiStore.get(parseJWTPayload(getJWTPayloadFromCookie()).user)._meta
      .load
    store.commit('login', user)
    return user
  } catch (e) {
    if (e.response && [401, 403, 404].includes(e.response.status)) {
      // 401 means no complete token was submitted, so we may be missing the JWT signature cookie
      // 403 means we can theoretically interact in some way with the user, but apparently not read it
      // 404 means the user doesn't exist or we don't have access to it
      // Either way, we aren't allowed to access the user from the token, so it's best to ask the user
      // to log in again.
      auth.logout()
      return null
    }

    throw e
  }
}

async function register(data) {
  const url = await apiStore.href(apiStore.get(), 'users')
  return apiStore.post(url, data)
}

async function redirectToOAuthLogin(provider) {
  let returnUrl = window.location.origin + router.resolve({ name: 'loginCallback' }).href

  const params = new URLSearchParams(window.location.search)
  if (params.has('redirect')) {
    returnUrl += '?redirect=' + params.get('redirect')
  }

  return apiStore
    .href(apiStore.get(), provider, { callback: encodeURI(returnUrl) })
    .then((url) => {
      window.location.href = window.environment.API_ROOT_URL + url
    })
}

async function loginGoogle() {
  return redirectToOAuthLogin('oauthGoogle')
}

async function loginPbsMiData() {
  return redirectToOAuthLogin('oauthPbsmidata')
}

async function loginCeviDB() {
  return redirectToOAuthLogin('oauthCevidb')
}

export async function logout() {
  Cookies.remove(headerAndPayloadCookieName(), {
    domain: window.environment.SHARED_COOKIE_DOMAIN,
  })
  store.commit('logout')
  return router
    .push({ name: 'login' })
    .catch(() => {}) // prevents throwing NavigationDuplicated is already on /login
    .then(() => apiStore.purgeAll())
    .then(() => isLoggedIn())
}

function headerAndPayloadCookieName() {
  return `${cookiePrefix()}jwt_hp`
}

function cookiePrefix() {
  return window.environment.COOKIE_PREFIX || ''
}

export const auth = {
  isLoggedIn,
  isAdmin,
  login,
  register,
  loginGoogle,
  loginPbsMiData,
  loginCeviDB,
  logout,
  loadUser,
  resetPasswordRequest,
  resetPassword,
}

class AuthPlugin {
  install(Vue) {
    Object.defineProperties(Vue.prototype, {
      $auth: {
        get() {
          return auth
        },
      },
    })
  }
}

export default new AuthPlugin()
