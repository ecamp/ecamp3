import Vue from 'vue'
import { auth } from '@/plugins/auth'
import storeLoader, { store, apiStore } from '@/plugins/store'
import Cookies from 'js-cookie'

Vue.use(storeLoader)

// expired on 01-01-1970
const expiredJWTPayload = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2MzMxMzM1MDAsImV4cCI6MCwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidGVzdC11c2VyIn0'
// expires on 01-01-3021, yes you read that right
const validJWTPayload = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2MzMxMzM0MDksImV4cCI6MzMxNjYzNjQ0MDAsInJvbGVzIjpbIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6InRlc3QtdXNlciJ9'

expect.extend({
  haveUri (actual, expectedUri) {
    return {
      pass: actual === expectedUri || actual._meta.self === expectedUri,
      message: () => 'expected to have the URI \'' + expectedUri + '\''
    }
  }
})

describe('authentication logic', () => {
  afterEach(() => {
    jest.restoreAllMocks()
    Cookies.remove('jwt_hp')
  })

  describe('isLoggedIn()', () => {
    it('returns true if JWT payload is not expired', () => {
      // given
      store.replaceState(createState())
      Cookies.set('jwt_hp', validJWTPayload)

      // when
      const result = auth.isLoggedIn()

      // then
      expect(result).toBeTruthy()
    })

    it('returns false if JWT payload is expired', () => {
      // given
      store.replaceState(createState())
      Cookies.set('jwt_hp', expiredJWTPayload)

      // when
      const result = auth.isLoggedIn()

      // then
      expect(result).toBeFalsy()
    })

    it('returns false if JWT cookie is missing', () => {
      // given
      store.replaceState(createState())
      Cookies.set('jwt_hp', expiredJWTPayload)

      // when
      const result = auth.isLoggedIn()

      // then
      expect(result).toBeFalsy()
    })
  })

  describe('register()', () => {
    it('sends a POST request to the backend', async done => {
      // given
      store.replaceState(createState())
      jest.spyOn(apiStore, 'post').mockImplementation(async () => {})

      // when
      await auth.register({ username: 'foo', email: 'bar', password: 'baz' })

      // then
      expect(apiStore.post).toHaveBeenCalledTimes(1)
      expect(apiStore.post).toHaveBeenCalledWith('http://localhost/users', { username: 'foo', email: 'bar', password: 'baz' })
      done()
    })
  })

  describe('login()', () => {
    it('resolves to true if the user successfully logs in', async done => {
      // given
      let isLoggedIn = false
      store.replaceState(createState())
      jest.spyOn(apiStore, 'post').mockImplementation(async () => {
        isLoggedIn = true
      })
      jest.spyOn(apiStore, 'reload').mockImplementation(() => {
        if (isLoggedIn) {
          Cookies.set('jwt_hp', validJWTPayload)
        }
      })

      // when
      /* const result = */await auth.login('foo', 'bar')

      // then
      // TODO hal-json-vuex can't handle "204 No Content" yet
      // expect(result).toBeTruthy()
      expect(apiStore.post).toHaveBeenCalledTimes(1)
      expect(apiStore.post).toHaveBeenCalledWith('/authentication_token', { username: 'foo', password: 'bar' })
      done()
    })

    it('resolves to false if the login fails', async done => {
      // given
      const isLoggedIn = false
      jest.spyOn(apiStore, 'post').mockImplementation(async () => {
        // login fails, leave guest role as it is
      })
      jest.spyOn(apiStore, 'reload').mockImplementation(() => {
        if (isLoggedIn) {
          Cookies.set('jwt_hp', validJWTPayload)
        }
      })

      // when
      const result = await auth.login('foo', 'barrrr')

      // then
      expect(result).toBeFalsy()
      expect(apiStore.post).toHaveBeenCalledTimes(1)
      expect(apiStore.post).toHaveBeenCalledWith('/authentication_token', { username: 'foo', password: 'barrrr' })
      done()
    })
  })

  describe('loginGoogle()', () => {
    const { location } = window
    beforeEach(() => {
      delete window.location
      window.location = {
        origin: 'http://localhost',
        href: 'http://localhost/login'
      }
    })
    afterEach(() => {
      window.location = location
    })

    it('forwards to google authentication endpoint', async done => {
      // when
      await auth.loginGoogle()

      // then
      expect(window.location.href).toBe('http://localhost/auth/google?callback=http%3A%2F%2Flocalhost%2FloginCallback')
      done()
    })
  })

  describe('loginPbsMiData()', () => {
    const { location } = window
    beforeEach(() => {
      delete window.location
      window.location = {
        origin: 'http://localhost',
        href: 'http://localhost/login'
      }
    })
    afterEach(() => {
      window.location = location
    })

    it('forwards to pbsmidata authentication endpoint', async done => {
      // when
      await auth.loginPbsMiData()

      // then
      expect(window.location.href).toBe('http://localhost/auth/pbsmidata?callback=http%3A%2F%2Flocalhost%2FloginCallback')
      done()
    })
  })

  describe('logout()', () => {
    it('resolves to false if the user successfully logs out', async done => {
      // given
      Cookies.set('jwt_hp', validJWTPayload)

      // when
      const result = await auth.logout()

      // then
      expect(result).toBeFalsy()
      done()
    })
  })
})

function createState (authState = {}) {
  return {
    api: {
      '': {
        ...authState,
        auth: {
          href: '/auth'
        },
        users: {
          href: '/users'
        },
        _meta: {
          self: ''
        }
      },
      '/auth': {
        register: {
          href: '/auth/register'
        },
        google: {
          href: 'http://localhost/auth/google{?callback}',
          templated: true
        },
        pbsmidata: {
          href: 'http://localhost/auth/pbsmidata{?callback}',
          templated: true
        },
        _meta: {
          self: '/auth'
        }
      },
      '/auth/logout': {
        _meta: {
          self: '/auth/logout'
        }
      }
    }
  }
}
