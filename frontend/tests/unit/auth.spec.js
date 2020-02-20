import { auth } from '@/auth'

import * as apiStore from '@/store'

const store = apiStore.default

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
      expect(apiStore.post.mock.calls.length).toEqual(1)
      expect(apiStore.post.mock.calls[0]).toEqual(['http://localhost/auth/register', { username: 'foo', email: 'bar', password: 'baz' }])
      done()
    })
  })

  describe('login()', () => {
    it('resolves to true if the user successfully logs in', async done => {
      // given
      store.replaceState(createState({role: 'guest'}))
      jest.spyOn(apiStore, 'post').mockImplementation(async () => {})
      jest.spyOn(apiStore, 'reload').mockImplementation(() => {
        store.replaceState(createState({role: 'user'}))
      })

      // when
      const result = await auth.login('foo', 'bar')

      // then
      expect(result).toBeTruthy()
      done()
    })

    it('resolves to false if the login fails', async done => {
      // given
      store.replaceState(createState({role: 'guest'}))
      jest.spyOn(apiStore, 'post').mockImplementation(async () => {})
      jest.spyOn(apiStore, 'reload').mockImplementation(() => {})

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
      store.replaceState(createState({role: 'guest'}))
      jest.spyOn(window, 'open').mockImplementation(() => window.afterLogin())
      jest.spyOn(apiStore, 'reload').mockImplementation(() => {
        store.replaceState(createState({role: 'user'}))
      })

      // when
      const result = await auth.loginGoogle()

      // then
      expect(result).toBeTruthy()
      done()
    })
  })

  describe('loginPbsMiData()', () => {
    it('resolves to true if the user successfully logs in', async done => {
      // given
      store.replaceState(createState({role: 'guest'}))
      jest.spyOn(window, 'open').mockImplementation(() => window.afterLogin())
      jest.spyOn(apiStore, 'reload').mockImplementation(() => {
        store.replaceState(createState({role: 'user'}))
      })

      // when
      const result = await auth.loginPbsMiData()

      // then
      expect(result).toBeTruthy()
      done()
    })
  })

  describe('logout()', () => {
    it('resolves to false if the user successfully logs out', async done => {
      // given
      store.replaceState(createState({role: 'user'}))
      jest.spyOn(apiStore, 'reload').mockImplementation(() => {
        store.replaceState(createState({role: 'guest'}))
        return { _meta: { load: Promise.resolve() }}
      })

      // when
      const result = await auth.logout()

      // then
      expect(result).toBeFalsy()
      done()
    })

    it('resolves to true if the logout fails', async done => {
      // given
      store.replaceState(createState({role: 'user'}))
      jest.spyOn(apiStore, 'reload').mockImplementation(() => ({ _meta: { load: Promise.resolve() }}))

      // when
      const result = await auth.logout()

      // then
      expect(result).toBeTruthy()
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
