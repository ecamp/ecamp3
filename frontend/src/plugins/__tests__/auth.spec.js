import Vue from 'vue'
import { auth } from '@/plugins/auth'
import storeLoader, { store, apiStore } from '@/plugins/store'
import Cookies from 'js-cookie'

Vue.use(storeLoader)

// expired on 01-01-1970
const expiredJWTPayload = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2MzMxMzM0MDksImV4cCI6MCwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidGVzdC11c2VyIiwidXNlciI6Ii91c2Vycy8xYTJiM2M0ZCJ9'
// expires on 01-01-3021, yes you read that right
const validJWTPayload = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2MzMxMzM0MDksImV4cCI6MzMxNjYzNjQ0MDAsInJvbGVzIjpbIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6InRlc3QtdXNlciIsInVzZXIiOiIvdXNlcnMvMWEyYjNjNGQifQ'

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
    it('sends a POST request to the API', async done => {
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
      store.replaceState(createState())
      jest.spyOn(apiStore, 'post').mockImplementation(async () => {
        Cookies.set('jwt_hp', validJWTPayload)
      })

      // when
      const result = await auth.login('foo', 'bar')

      // then
      expect(result).toBeTruthy()
      expect(apiStore.post).toHaveBeenCalledTimes(1)
      expect(apiStore.post).toHaveBeenCalledWith('/authentication_token', { username: 'foo', password: 'bar' })
      done()
    })

    it('resolves to false if the login fails', async done => {
      // given
      jest.spyOn(apiStore, 'post').mockImplementation(async () => {
        // login fails, no cookie added
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

  describe('user()', () => {
    it('resolves to null if not logged in', async done => {
      // given
      store.replaceState(createState())
      jest.spyOn(apiStore, 'get')

      // when
      const result = auth.user()

      // then
      expect(result).toEqual(null)
      expect(apiStore.get).toHaveBeenCalledTimes(0)
      done()
    })

    it('resolves to the user from the JWT token cookie', async done => {
      // given
      store.replaceState(createState())
      const user = {
        username: 'something',
        _meta: {}
      }
      user._meta.load = new Promise(() => user)
      Cookies.set('jwt_hp', validJWTPayload)

      jest.spyOn(apiStore, 'get').mockImplementation(() => user)

      // when
      const result = auth.user()

      // then
      expect(result).toEqual(user)
      expect(apiStore.get).toHaveBeenCalledTimes(1)
      expect(apiStore.get).toHaveBeenCalledWith('/users/1a2b3c4d')
      done()
    })

    it.each([[401], [403], [404]])('calls logout when fetching the user fails with status %s', async (status, done) => {
      // given
      store.replaceState(createState())
      Cookies.set('jwt_hp', validJWTPayload)

      const user = {
        _meta: {
          load: new Promise(() => {
            const error = new Error('test error')
            error.response = { status }
            throw error
          })
        }
      }
      jest.spyOn(apiStore, 'get').mockImplementation(() => user)
      jest.spyOn(auth, 'logout').mockImplementation(() => user)

      // when
      const result = auth.user()

      // then
      expect(result).toEqual(user)
      expect(apiStore.get).toHaveBeenCalledTimes(1)
      expect(apiStore.get).toHaveBeenCalledWith('/users/1a2b3c4d')
      await result._meta.load
      expect(auth.logout).toHaveBeenCalledTimes(1)
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
