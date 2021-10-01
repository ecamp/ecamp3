import Vue from 'vue'
import { auth } from '@/plugins/auth'
import storeLoader, { store, apiStore } from '@/plugins/store'

Vue.use(storeLoader)

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
  })

  // TODO write new tests for the new isLoggedIn implementation based on the jwt_hp cookie
  describe.skip('isLoggedIn()', () => {
    it('returns true if authenticated is true', () => {
      // given
      store.replaceState(createState({ authenticated: true }))

      // when
      const result = auth.isLoggedIn()

      // then
      expect(result).toBeTruthy()
    })

    it('returns false if the authenticated is false', () => {
      // given
      store.replaceState(createState({ authenticated: false }))

      // when
      const result = auth.isLoggedIn()

      // then
      expect(result).toBeFalsy()
    })

    it('returns false if authenticated is undefined', () => {
      // given
      store.replaceState(createState({ authenticated: undefined }))

      // when
      const result = auth.isLoggedIn()

      // then
      expect(result).toBeFalsy()
    })
  })

  describe('register()', () => {
    it('sends a POST request to the backend', async done => {
      // given
      store.replaceState(createState({ authenticated: false }))
      jest.spyOn(apiStore, 'post').mockImplementation(async () => {})

      // when
      await auth.register({ username: 'foo', email: 'bar', password: 'baz' })

      // then
      expect(apiStore.post).toHaveBeenCalledTimes(1)
      expect(apiStore.post).toHaveBeenCalledWith('http://localhost/auth/register', { username: 'foo', email: 'bar', password: 'baz' })
      done()
    })
  })

  describe('login()', () => {
    it('resolves to true if the user successfully logs in', async done => {
      // given
      let isLoggedIn = false
      store.replaceState(createState({ authenticated: isLoggedIn }))
      jest.spyOn(apiStore, 'post').mockImplementation(async () => {
        isLoggedIn = true
      })
      jest.spyOn(apiStore, 'reload').mockImplementation(() => {
        store.replaceState(createState({ authenticated: isLoggedIn }))
      })

      // when
      const result = await auth.login('foo', 'bar')

      // then
      expect(result).toBeTruthy()
      expect(apiStore.post).toHaveBeenCalledTimes(1)
      expect(apiStore.post).toHaveBeenCalledWith('http://localhost/auth/login', { username: 'foo', password: 'bar' })
      done()
    })

    it('resolves to false if the login fails', async done => {
      // given
      const isLoggedIn = false
      store.replaceState(createState({ authenticated: isLoggedIn }))
      jest.spyOn(apiStore, 'post').mockImplementation(async () => {
        // login fails, leave guest role as it is
      })
      jest.spyOn(apiStore, 'reload').mockImplementation(() => {
        store.replaceState(createState({ authenticated: isLoggedIn }))
      })

      // when
      const result = await auth.login('foo', 'barrrr')

      // then
      expect(result).toBeFalsy()
      expect(apiStore.post).toHaveBeenCalledTimes(1)
      expect(apiStore.post).toHaveBeenCalledWith('http://localhost/auth/login', { username: 'foo', password: 'barrrr' })
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
      let isLoggedIn = true
      store.replaceState(createState({ authenticated: isLoggedIn }))
      jest.spyOn(apiStore, 'reload').mockImplementation(arg => {
        if (arg._meta.self === 'http://localhost/auth/logout') isLoggedIn = false
        store.replaceState(createState({ authenticated: isLoggedIn }))
        return Promise.resolve()
      })

      // when
      const result = await auth.logout()

      // then
      expect(result).toBeFalsy()
      done()
    })
  })
})

function createState (authState) {
  return {
    api: {
      '': {
        ...authState,
        auth: {
          href: '/auth'
        },
        _meta: {
          self: ''
        }
      },
      '/auth': {
        login: {
          href: '/auth/login'
        },
        logout: {
          href: '/auth/logout'
        },
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
