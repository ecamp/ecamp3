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

async function isLoggedIn () {
  const savedStatus = window.localStorage.getItem(STORAGE_LOCATION)
  if (savedStatus !== null) {
    return savedStatus === LOGGED_IN
  }
  const response = await get()._meta.loaded
  const loginStatus = (response.user.toString() !== 'guest') ? LOGGED_IN : LOGGED_OUT
  window.localStorage.setItem(STORAGE_LOCATION, loginStatus)
  return loginStatus === LOGGED_IN
}

async function login (username, password, callback) {
  const url = await href(get().login(), 'native')
  return post(url, { username: username, password: password })
    .then(async resp => {
      if (resp.user !== 'guest') {
        await loginSuccess()
        callback && callback()
        return true
      } else {
        callback && callback()
        return false
      }
    })
}

async function loginGoogle (callback) {
  // Make the login callback function available on global level, so the popup can call it
  window.loginSuccess = async () => {
    await loginSuccess()
    callback && callback()
  }

  const returnUrl = window.location.origin + router.resolve({ name: 'loginCallback' }).href
  // TODO use templated relations if #369 is implemented
  const url = (await href(get().login(), 'google')) + '?callback=' + encodeURI(returnUrl)
  window.open(url, '', 'width=500px,height=600px')
}

async function loginPbsMiData (callback) {
  // Make the login callback function available on global level, so the popup can call it
  window.loginSuccess = async () => {
    await loginSuccess()
    callback && callback()
  }

  const returnUrl = window.location.origin + router.resolve({ name: 'loginCallback' }).href
  // TODO use templated relations if #369 is implemented
  const url = (await href(get().login(), 'pbsmidata')) + '?callback=' + encodeURI(returnUrl)
  window.open(url, '', 'width=500px,height=600px')
}

async function loginSuccess () {
  window.localStorage.setItem(STORAGE_LOCATION, LOGGED_IN)
  // refresh the available login options
  await reload(get().login())._meta.loaded
  notifySubscribers(true)
}

async function logout (callback) {
  await get().login().logout()._meta.loaded
  window.localStorage.setItem(STORAGE_LOCATION, LOGGED_OUT)
  // refresh the available login options
  await reload(get().login())._meta.loaded
  notifySubscribers(false)
  callback && callback()
}

export const auth = { isLoggedIn, subscribe, login, loginGoogle, loginPbsMiData, loginSuccess, logout }

Vue.auth = auth
Object.defineProperties(Vue.prototype, {
  $auth: {
    get () {
      return auth
    }
  }
})
