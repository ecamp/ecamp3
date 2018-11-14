export function isLoggedIn () {
  return loggedInUser() != null
}

export function loggedInUser () {
  return window.localStorage.getItem('loggedInUser')
}

export function login (http, successCallback, errorCallback) {
  http.get(process.env.VUE_APP_ROOT_API + '/login/google').then(result => {
    window.localStorage.setItem('loggedInUser', result)
    successCallback(result)
  }).catch(error => {
    console.error('Error logging in with the API', error)
    errorCallback(error)
  })
}

export function logout (http, successCallback, errorCallback) {
  http.get(process.env.VUE_APP_ROOT_API + '/logout').then(result => {
    window.localStorage.removeItem('loggedInUser')
    successCallback(result)
  }).catch(error => {
    console.error('Error logging out', error)
    errorCallback(error)
  })
}
