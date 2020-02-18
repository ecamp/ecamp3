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
  const loginStatus = (await reload(get().login())._meta.loaded).role === 'user'
  window.localStorage.setItem(STORAGE_LOCATION, loginStatus ? LOGGED_IN : LOGGED_OUT)
  return loginStatus
}

async function login (username, password) {
  const url = await href(get().login(), 'native')
  return post(url, { username: username, password: password }).then(() => loginStatusChange())
}

function loginInSeparateWindow (provider) {
  return async resolve => {
    // Make the promise resolve function available on global level, so the separate window can call it
    window.afterLogin = resolve
    const returnUrl = window.location.origin + router.resolve({ name: 'loginCallback' }).href
    // TODO use templated relations once #369 is implemented
    const url = (await href(get().login(), provider)) + '?callback=' + encodeURI(returnUrl)
    window.open(url, '', 'width=500px,height=600px')
  }
}

async function loginGoogle () {
  return new Promise(loginInSeparateWindow('google')).then(() => loginStatusChange())
}

async function loginPbsMiData () {
  return new Promise(loginInSeparateWindow('pbsmidata')).then(() => loginStatusChange())
}

async function logout () {
  return reload(get().login().logout())._meta.loaded.then(() => loginStatusChange())
}

async function loginStatusChange () {
  const loginStatus = await isLoggedIn(true)
  notifySubscribers(loginStatus)
  return loginStatus
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
