import { auth } from '@/auth'

import * as apiStore from '@/store'

const store = apiStore.default

expect.extend({
  haveUri (actual, expectedUri) {
      return {
        pass: actual === expectedUri || actual._meta.self === expectedUri,
        message: () => 'expected to have the URI \'' + expectedUri + '\'',
      };
  },
});

describe('authentication logic', () => {

  beforeEach(() => {
    jest.restoreAllMocks()
  })

  describe('isLoggedIn()', () => {
    it('returns true if the role on the auth endpoint is "user"', () => {
      // given
      store.replaceState(createState({ role: 'user' }))

      // when
      const result = auth.isLoggedIn()

      // then
      expect(result).toBeTruthy()
    })

    it('returns false if the role on the auth endpoint is "guest"', () => {
      // given
      store.replaceState(createState({ role: 'guest' }))

      // when
      const result = auth.isLoggedIn()

      // then
      expect(result).toBeFalsy()
    })

    it('returns false if the role on the auth endpoint is undefined', () => {
      // given
      store.replaceState(createState({ role: undefined }))

      // when
      const result = auth.isLoggedIn()

      // then
      expect(result).toBeFalsy()
    })
  })

  describe('refreshLoginStatus()', () => {
    it('resolves to true if the role on the auth endpoint is "user"', async done => {
      // given
      store.replaceState(createState({ role: 'user' }))
      jest.spyOn(apiStore, 'reload').mockImplementation(() => {})

      // when
      const result = await auth.refreshLoginStatus()

      // then
      expect(result).toBeTruthy()
      done()
    })

    it('resolves to false if the role on the auth endpoint is "guest"', async done => {
      // given
      store.replaceState(createState({ role: 'guest' }))
      jest.spyOn(apiStore, 'reload').mockImplementation(() => {})

      // when
      const result = await auth.refreshLoginStatus()

      // then
      expect(result).toBeFalsy()
      done()
    })

    it('resolves to false if the user has just signed out', async done => {
      // given
      store.replaceState(createState({ role: 'user' }))
      jest.spyOn(apiStore, 'reload').mockImplementation(() => {
        store.replaceState(createState({ role: 'guest'}))
      })

      // when
      const result = await auth.refreshLoginStatus()

      // then
      expect(result).toBeFalsy()
      done()
    })

    it('resolves to true if the user has just signed in', async done => {
      // given
      store.replaceState(createState({ role: 'guest' }))
      jest.spyOn(apiStore, 'reload').mockImplementation(() => {
        store.replaceState(createState({ role: 'user'}))
      })

      // when
      const result = await auth.refreshLoginStatus()

      // then
      expect(result).toBeTruthy()
      done()
    })
  })

  describe('register()', () => {
    it('sends a POST request to the backend', async done => {
      // given
      store.replaceState(createState({role: 'guest'}))
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
      let roleInBackend = 'guest'
      store.replaceState(createState({ role: roleInBackend }))
      jest.spyOn(apiStore, 'post').mockImplementation(async () => {
        roleInBackend = 'user'
      })
      jest.spyOn(apiStore, 'reload').mockImplementation(() => {
        store.replaceState(createState({ role: roleInBackend }))
      })

      // when
      const result = await auth.login('foo', 'bar')

      // then
      expect(result).toBeTruthy()
      done()
    })

    it('resolves to false if the login fails', async done => {
      // given
      let roleInBackend = 'guest'
      store.replaceState(createState({ role: roleInBackend }))
      jest.spyOn(apiStore, 'post').mockImplementation(async () => {
        // login fails, leave guest role as it is
      })
      jest.spyOn(apiStore, 'reload').mockImplementation(() => {
        store.replaceState(createState({ role: roleInBackend }))
      })

      // when
      const result = await auth.login('foo', 'barrrr')

      // then
      expect(result).toBeFalsy()
      done()
    })
  })

  describe('loginGoogle()', () => {
    it('resolves to true if the user successfully logs in', async done => {
      // given
      let roleInBackend = 'guest'
      store.replaceState(createState({ role: roleInBackend }))
      jest.spyOn(window, 'open').mockImplementation(() => {
        roleInBackend = 'user'
        window.afterLogin()
      })
      jest.spyOn(apiStore, 'reload').mockImplementation(() => {
        store.replaceState(createState({ role: roleInBackend }))
      })

      // when
      const result = await auth.loginGoogle()

      // then
      expect(result).toBeTruthy()
      expect(window.open).toHaveBeenCalledTimes(1)
      expect(window.open).toHaveBeenCalledWith('http://localhost/auth/google?callback=http://localhost/loginCallback', expect.anything(), expect.anything())
      done()
    })
  })

  describe('loginPbsMiData()', () => {
    it('resolves to true if the user successfully logs in', async done => {
      // given
      let roleInBackend = 'guest'
      store.replaceState(createState({ role: roleInBackend }))
      jest.spyOn(window, 'open').mockImplementation(() => {
        roleInBackend = 'user'
        window.afterLogin()
      })
      jest.spyOn(apiStore, 'reload').mockImplementation(() => {
        store.replaceState(createState({ role: roleInBackend }))
      })

      // when
      const result = await auth.loginPbsMiData()

      // then
      expect(result).toBeTruthy()
      expect(window.open).toHaveBeenCalledTimes(1)
      expect(window.open).toHaveBeenCalledWith('http://localhost/auth/pbsmidata?callback=http://localhost/loginCallback', expect.anything(), expect.anything())
      done()
    })
  })

  describe('logout()', () => {
    it('resolves to false if the user successfully logs out', async done => {
      // given
      let roleInBackend = 'user'
      store.replaceState(createState({ role: roleInBackend }))
      jest.spyOn(apiStore, 'reload').mockImplementation(arg => {
        if (arg._meta.self === 'http://localhost/auth/logout') roleInBackend = 'guest'
        store.replaceState(createState({ role: roleInBackend }))
        return { _meta: { load: Promise.resolve() }}
      })

      // when
      const result = await auth.logout()

      // then
      expect(result).toBeFalsy()
      expect(apiStore.reload).toHaveBeenCalledWith(expect.haveUri('http://localhost/auth/logout'))
      done()
    })

    it('resolves to true if the logout fails', async done => {
      // given
      store.replaceState(createState({role: 'user'}))
      jest.spyOn(apiStore, 'reload').mockImplementation(() => {
        // logout fails, leave user role as it is
        return { _meta: { load: Promise.resolve() }}
      })

      // when
      const result = await auth.logout()

      // then
      expect(result).toBeTruthy()
      expect(apiStore.reload).toHaveBeenCalledWith(expect.haveUri('http://localhost/auth/logout'))
      done()
    })
  })
})

function createState(authState) {
  return {
    'api': {
      '': {
        auth: {
          href: '/auth'
        },
        _meta: {
          self: ''
        }
      },
      '/auth': {
        ...authState,
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
          href: '/auth/google'
        },
        pbsmidata: {
          href: '/auth/pbsmidata'
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
