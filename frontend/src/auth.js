const storageLocation = 'loggedInUserToken'

export function isLoggedIn () {
  return loggedInUserToken() != null
}

export function loggedInUserToken () {
  return window.localStorage.getItem(storageLocation)
}

export function login () {
  window.open(process.env.VUE_APP_ROOT_API + '/login/google?redirect=' + encodeURI(window.location.href), '', 'width=500px,height=600px')
}

export function loginSuccess (jwt) {
  window.localStorage.setItem(storageLocation, jwt)
  var user = JSON.parse(atob(jwt.split('.')[1]))
  console.log(user)
}

export function logout () {
  window.localStorage.removeItem(storageLocation)
}
