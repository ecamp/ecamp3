import { apiStore, store } from '@/plugins/store'
import {
  hasLoggedOutFromLocalStorage,
  getRefreshTokenExpiresAt,
} from '@/plugins/store/auth.js'
import router from '@/router'
import Cookies from 'js-cookie'
import { getEnv } from '@/environment.js'
import { isNavigationFailure, NavigationFailureType } from 'vue-router'

const REFRESH_PATH = '/token/refresh'

let scheduledRefresh = null
let refreshTokenPromise = null
let reAuthPromise = null
let reAuthResolve = null
let reAuthReject = null

function waitForReAuth() {
  if (!reAuthPromise) {
    reAuthPromise = new Promise((resolve, reject) => {
      reAuthResolve = resolve
      reAuthReject = reject
    })
  }
  return reAuthPromise
}

export function resolveReAuth() {
  reAuthResolve?.()
  reAuthPromise = null
  reAuthResolve = null
  reAuthReject = null
  store.commit('setAuthRequired', false)
}

export function rejectReAuth() {
  reAuthReject?.(new Error('Re-authentication cancelled'))
  reAuthPromise = null
  reAuthResolve = null
  reAuthReject = null
  store.commit('setAuthRequired', false)
}

export function isRefreshLikelyPossible() {
  const refreshTokenExpiry = getRefreshTokenExpiresAt()
  if (refreshTokenExpiry === 0) return false
  return Date.now() < refreshTokenExpiry
}

function isRefreshRequest(config) {
  return !!config?.url && config.url.includes(REFRESH_PATH)
}

export function setupAxiosAuthInterceptor(axiosInstance) {
  axiosInstance.interceptors.response.use(null, async (error) => {
    const request = error.config
    const status = error.response?.status ?? error.status

    if (
      status !== 401 ||
      !request ||
      request._authRetried ||
      isRefreshRequest(request) ||
      hasLoggedOutFromLocalStorage()
    ) {
      return Promise.reject(error)
    }

    request._authRetried = true

    try {
      await refreshAuthTokenSingleton()
    } catch {
      store.commit('setAuthRequired', true)
      try {
        await waitForReAuth()
      } catch {
        return Promise.reject(error)
      }
      return axiosInstance(request)
    }

    rescheduleRefresh()
    return axiosInstance(request)
  })
}

function refreshAuthTokenSingleton() {
  async function refresh() {
    const url = await apiStore.href(apiStore.get(), 'refreshToken')
    return apiStore.post(url)
  }

  if (!refreshTokenPromise) {
    refreshTokenPromise = refresh().finally(() => {
      refreshTokenPromise = null
    })
  }
  return refreshTokenPromise
}

export async function initRefresh() {
  try {
    // Cookies.get was not reliable to detect if the cookie was present.
    if (hasLoggedOutFromLocalStorage()) {
      return
    }
    let originalTarget = `${window.location.pathname}`
    if (window.location.search) {
      originalTarget += `?${window.location.search}`
    }
    let refreshedSuccessfully = false
    if (!isLoggedIn()) {
      if (getRefreshTokenExpiresAt() === 0) {
        return
      }
      try {
        await refreshAuthTokenSingleton()
      } catch {
        /* empty */
      }
      if (!isLoggedIn()) {
        return
      }
      refreshedSuccessfully = true
    }
    rescheduleRefresh()
    if (refreshedSuccessfully) {
      await router.replace(originalTarget).catch((e) => {
        // Silently ignore if we are already at that target
        if (!isNavigationFailure(e, NavigationFailureType.duplicated)) {
          return Promise.reject(e)
        }
      })
    }
  } finally {
    store.commit('setAuthInitializing', false)
  }
}

function rescheduleRefresh() {
  if (scheduledRefresh != null) {
    clearTimeout(scheduledRefresh)
  }
  store.commit('setRefreshTokenExpiresAt', Date.now() + getEnv().REFRESH_TOKEN_TTL * 1000)
  const timeout = (getJWTExpirationTimestamp() - Date.now()) / 2
  const realTimeout = Math.max(Math.min(timeout, 30 * 60 * 1000), 2 * 60 * 1000)
  scheduledRefresh = setTimeout(refreshAndSchedule, realTimeout)
}

async function refreshAndSchedule() {
  await refreshAuthTokenSingleton()
  rescheduleRefresh()
}

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

export function getJWTExpirationTimestamp() {
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
    rescheduleRefresh()
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

async function resendUserActivation(email, recaptchaToken) {
  const url = await apiStore.href(apiStore.get(), 'resendActivation')
  return apiStore.post(url, { email: email, recaptchaToken: recaptchaToken })
}

async function loadUser() {
  if (!getJWTPayloadFromCookie()) {
    store.commit('logout')
    return null
  }

  try {
    const profiles = await apiStore
      .get()
      .profiles({ user: parseJWTPayload(getJWTPayloadFromCookie()).user })._meta.load
    store.commit('login', profiles.items[0].user())
    return profiles.items[0].user()
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
      window.location.href = url
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

async function loginJublaDB() {
  return redirectToOAuthLogin('oauthJubladb')
}

export async function logout() {
  if (scheduledRefresh != null) {
    clearTimeout(scheduledRefresh)
  }
  Cookies.remove(headerAndPayloadCookieName())
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
  return getEnv().COOKIE_PREFIX || ''
}

export const auth = {
  initRefresh,
  isLoggedIn,
  isAdmin,
  login,
  register,
  loginGoogle,
  loginPbsMiData,
  loginCeviDB,
  loginJublaDB,
  logout,
  loadUser,
  resetPasswordRequest,
  resetPassword,
  resendUserActivation,
}

class AuthPlugin {
  install(app) {
    Object.defineProperties(app.config.globalProperties, {
      $auth: {
        get() {
          return auth
        },
      },
    })
  }
}

export default new AuthPlugin()
