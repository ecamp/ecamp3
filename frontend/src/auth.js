import Vue from 'vue'
import axios from 'axios'
import { get, reload, post, href } from '@/store'
import router from '@/router'

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

async function isLoggedIn (forceReload = false) {
  if (forceReload) reload(get().auth())
  return (await get().auth()._meta.loaded).role === 'user'
}

async function login (username, password) {
  const url = await href(get().auth(), 'login')
  return post(url, { username: username, password: password }).then(() => loginStatusChange())
}

function loginInSeparateWindow (provider) {
  return async resolve => {
    // Make the promise resolve function available on global level, so the separate window can call it
    window.afterLogin = resolve
    const returnUrl = window.location.origin + router.resolve({ name: 'loginCallback' }).href
    // TODO use templated relations once #369 is implemented
    const url = (await href(get().auth(), provider)) + '?callback=' + encodeURI(returnUrl)
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
  return reload(get().auth().logout())._meta.loaded.then(() => loginStatusChange())
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
