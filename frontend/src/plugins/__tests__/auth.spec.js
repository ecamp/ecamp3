import Vue from 'vue'
import { auth } from '@/plugins/auth'
import storeLoader, { store, apiStore } from '@/plugins/store'
import Cookies from 'js-cookie'
import axios from 'axios'

Vue.use(storeLoader)

// expired on 01-01-1970
const expiredJWTPayload =
  'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2MzMxMzM0MDksImV4cCI6MCwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidGVzdC11c2VyIiwidXNlciI6Ii91c2Vycy8xYTJiM2M0ZCJ9'
// {
//   "iat": 1633133409,
//   "exp": 0,
//   "roles": [
//     "ROLE_USER"
//   ],
//   "username": "test-user",
//   "user": "/users/1a2b3c4d"
// }

// expires on 01-01-3021, yes you read that right
const validJWTPayload =
  'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2MzMxMzM0MDksImV4cCI6MzMxNjYzNjQ0MDAsInJvbGVzIjpbIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6InRlc3QtdXNlciIsInVzZXIiOiIvdXNlcnMvMWEyYjNjNGQifQ'
// {
//   "iat": 1633133409,
//   "exp": 33166364400,
//   "roles": [
//     "ROLE_USER"
//   ],
//   "username": "test-user",
//   "user": "/users/1a2b3c4d"
// }

expect.extend({
  haveUri(actual, expectedUri) {
    return {
      pass: actual === expectedUri || actual._meta.self === expectedUri,
      message: () => "expected to have the URI '" + expectedUri + "'",
    }
  },
})

describe('authentication logic', () => {
  afterEach(() => {
    jest.restoreAllMocks()
    Cookies.remove('localhost_jwt_hp')
  })

  describe('isLoggedIn()', () => {
    it('returns true if JWT payload is not expired', () => {
      // given
      store.replaceState(createState())
      Cookies.set('localhost_jwt_hp', validJWTPayload)

      // when
      const result = auth.isLoggedIn()

      // then
      expect(result).toBeTruthy()
    })

    it('returns false if JWT payload is expired', () => {
      // given
      store.replaceState(createState())
      Cookies.set('localhost_jwt_hp', expiredJWTPayload)

      // when
      const result = auth.isLoggedIn()

      // then
      expect(result).toBeFalsy()
    })

    it('returns false if JWT cookie is missing', () => {
      // given
      store.replaceState(createState())
      Cookies.set('localhost_jwt_hp', expiredJWTPayload)

      // when
      const result = auth.isLoggedIn()

      // then
      expect(result).toBeFalsy()
    })
  })

  describe('register()', () => {
    it('sends a POST request to the API', async () => {
      // given
      store.replaceState(createState())
      jest.spyOn(apiStore, 'post').mockImplementation(async () => {})

      // when
      await auth.register({ email: 'bar', password: 'baz' })

      // then
      expect(apiStore.post).toHaveBeenCalledTimes(1)
      expect(apiStore.post).toHaveBeenCalledWith('/users', {
        email: 'bar',
        password: 'baz',
      })
    })
  })

  describe('login()', () => {
    it('resolves to true if the user successfully logs in', async () => {
      // given
      store.replaceState(createState())
      jest.spyOn(axios, 'post').mockImplementation(async () => {
        Cookies.set('localhost_jwt_hp', validJWTPayload)
      })

      // when
      const result = await auth.login('foo', 'bar')

      // then
      expect(result).toBeTruthy()
      expect(axios.post).toHaveBeenCalledTimes(1)
      expect(axios.post).toHaveBeenCalledWith('/authentication_token', {
        identifier: 'foo',
        password: 'bar',
      })
    })

    it('resolves to false if the login fails', async () => {
      // given
      jest.spyOn(axios, 'post').mockImplementation(async () => {
        // login fails, no cookie added
      })

      // when
      const result = await auth.login('foo', 'barrrr')

      // then
      expect(result).toBeFalsy()
      expect(axios.post).toHaveBeenCalledTimes(1)
      expect(axios.post).toHaveBeenCalledWith('/authentication_token', {
        identifier: 'foo',
        password: 'barrrr',
      })
    })
  })

  describe('loadUser()', () => {
    it('resolves to null if not logged in', async () => {
      // given
      store.replaceState(createState())
      jest.spyOn(apiStore, 'get')

      // when
      const result = await auth.loadUser()

      // then
      expect(result).toEqual(null)
      expect(apiStore.get).toHaveBeenCalledTimes(0)
    })

    it('resolves to the user from the JWT token cookie', async () => {
      // given
      store.replaceState(createState())
      Cookies.set('localhost_jwt_hp', validJWTPayload)
      jest.spyOn(apiStore, 'get')

      // when
      const result = await auth.loadUser()

      // then
      expect(result.id).toEqual('1a2b3c4d')
      expect(apiStore.get).toHaveBeenCalledTimes(1)
      expect(apiStore.get).toHaveBeenCalledWith('/users/1a2b3c4d')
    })

    it.each([[401], [403], [404]])(
      'calls logout when fetching the user fails with status %s',
      async (status) => {
        // given
        store.replaceState(createState())
        Cookies.set('localhost_jwt_hp', validJWTPayload)

        const user = {
          _meta: {
            load: new Promise(() => {
              const error = new Error('test error')
              error.response = { status }
              throw error
            }),
          },
        }
        jest.spyOn(apiStore, 'get').mockImplementation(() => user)
        jest.spyOn(auth, 'logout')

        // when
        const result = await auth.loadUser()

        // then
        expect(result).toEqual(null)
        expect(apiStore.get).toHaveBeenCalledTimes(1)
        expect(apiStore.get).toHaveBeenCalledWith('/users/1a2b3c4d')
        expect(auth.logout).toHaveBeenCalledTimes(1)
      }
    )
  })

  describe('loginGoogle()', () => {
    const { location } = window
    beforeEach(() => {
      delete window.location
      window.location = {
        origin: 'http://localhost',
        href: 'http://localhost/login',
      }
      store.replaceState(createState())
    })
    afterEach(() => {
      window.location = location
    })

    it('forwards to google authentication endpoint', async () => {
      // when
      await auth.loginGoogle()

      // then
      expect(window.location.href).toBe(
        'http://localhost/auth/google?callback=http%3A%2F%2Flocalhost%2FloginCallback'
      )
    })
  })

  describe('loginPbsMiData()', () => {
    const { location } = window
    beforeEach(() => {
      delete window.location
      window.location = {
        origin: 'http://localhost',
        href: 'http://localhost/login',
      }
      store.replaceState(createState())
    })
    afterEach(() => {
      window.location = location
    })

    it('forwards to pbsmidata authentication endpoint', async () => {
      // when
      await auth.loginPbsMiData()

      // then
      expect(window.location.href).toBe(
        'http://localhost/auth/pbsmidata?callback=http%3A%2F%2Flocalhost%2FloginCallback'
      )
    })
  })

  describe('loginCeviDB()', () => {
    const { location } = window
    beforeEach(() => {
      delete window.location
      window.location = {
        origin: 'http://localhost',
        href: 'http://localhost/login',
      }
      store.replaceState(createState())
    })
    afterEach(() => {
      window.location = location
    })

    it('forwards to cevidb authentication endpoint', async () => {
      // when
      await auth.loginCeviDB()

      // then
      expect(window.location.href).toBe(
        'http://localhost/auth/cevidb?callback=http%3A%2F%2Flocalhost%2FloginCallback'
      )
    })
  })

  describe('logout()', () => {
    it('resolves to false if the user successfully logs out', async () => {
      // given
      Cookies.set('localhost_jwt_hp', validJWTPayload)

      // when
      const result = await auth.logout()

      // then
      expect(result).toBeFalsy()
    })
  })
})

function createState(authState = {}) {
  return {
    auth: {
      user: null,
    },
    api: {
      '': {
        ...authState,
        users: {
          href: '/users',
        },
        login: {
          href: '/authentication_token',
        },
        oauthGoogle: {
          href: '/auth/google{?callback}',
          templated: true,
        },
        oauthPbsmidata: {
          href: '/auth/pbsmidata{?callback}',
          templated: true,
        },
        oauthCevidb: {
          href: '/auth/cevidb{?callback}',
          templated: true,
        },
        _meta: {
          self: '',
        },
      },
      '/users/1a2b3c4d': {
        id: '1a2b3c4d',
        _meta: {
          load: Promise.resolve({
            id: '1a2b3c4d',
          }),
        },
      },
    },
  }
}
