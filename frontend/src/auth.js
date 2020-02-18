import Vue from 'vue'
import axios from 'axios'
import { get, reload, post, href } from '@/store'
import router from '@/router'

const STORAGE_LOCATION = 'loggedIn'
const LOGGED_IN = '1'
const LOGGED_OUT = '0'

axios.interceptors.response.use(null, error => {
  if (error.status === 401) {
    logout()
  }
  return Promise.reject(error)
})

const subscribers = []

function subscribe (onLoginStatusChange) {
  subscribers.push(onLoginStatusChange)
}

function notifySubscribers (newLoginStatus) {
  subscribers.forEach(subscriber => subscriber(newLoginStatus))
}

async function isLoggedIn (ignoreLocalStorage = false) {
  if (!ignoreLocalStorage) {
    const savedStatus = window.localStorage.getItem(STORAGE_LOCATION)
    if (savedStatus !== null) {
      return savedStatus === LOGGED_IN
    }
  }
  const loginStatus = ((await reload(get().login())._meta.loaded).role === 'user') ? LOGGED_IN : LOGGED_OUT
  window.localStorage.setItem(STORAGE_LOCATION, loginStatus)
  return loginStatus === LOGGED_IN
}

async function login (username, password, callback) {
  const url = await href(get().login(), 'native')
  await post(url, { username: username, password: password })
  await loginStatusChange(callback)
}

async function loginGoogle (callback) {
  // Make the login callback function available on global level, so the popup can call it
  window.loginSuccess = async () => loginStatusChange(callback)

  const returnUrl = window.location.origin + router.resolve({ name: 'loginCallback' }).href
  // TODO use templated relations once #369 is implemented
  const url = (await href(get().login(), 'google')) + '?callback=' + encodeURI(returnUrl)
  window.open(url, '', 'width=500px,height=600px')
}

async function loginPbsMiData (callback) {
  // Make the login callback function available on global level, so the popup can call it
  window.loginSuccess = async () => loginStatusChange(callback)

  const returnUrl = window.location.origin + router.resolve({ name: 'loginCallback' }).href
  // TODO use templated relations once #369 is implemented
  const url = (await href(get().login(), 'pbsmidata')) + '?callback=' + encodeURI(returnUrl)
  window.open(url, '', 'width=500px,height=600px')
}

async function logout (callback) {
  await reload(get().login().logout())._meta.loaded
  await loginStatusChange(callback)
}

async function loginStatusChange (callback) {
  const loginStatus = await isLoggedIn(true)
  notifySubscribers(loginStatus)
  callback && callback(loginStatus)
}

export const auth = { isLoggedIn, subscribe, login, loginGoogle, loginPbsMiData, logout }

Vue.auth = auth
Object.defineProperties(Vue.prototype, {
  $auth: {
    get () {
      return auth
    }
  }
})
