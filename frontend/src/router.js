import Vue from 'vue'
import Router from 'vue-router'
import slugify from 'slugify'
import { refreshLoginStatus } from '@/plugins/auth'
import { apiStore } from '@/plugins/store'

Vue.use(Router)

const NavigationAuth = () => import(/* webpackChunkName: "navigationAuth" */ './views/auth/NavigationAuth')
const NavigationDefault = () => import(/* webpackChunkName: "navigationDefault" */ './views/NavigationDefault')
const NavigationCamp = () => import(/* webpackChunkName: "navigationCamp" */ './views/camp/NavigationCamp')

/* istanbul ignore next */
export default new Router({
  mode: 'history',
  base: window.environment.BASE_URL || '/',
  routes: [
    // Dev-Pages:
    {
      path: '/controls',
      name: 'controls',
      components: {
        navigation: NavigationDefault,
        default: () => import(/* webpackChunkName: "register" */ './views/dev/Controls')
      }
    },
    // Prod-Pages:
    {
      path: '/register',
      name: 'register',
      components: {
        navigation: NavigationAuth,
        default: () => import(/* webpackChunkName: "register" */ './views/auth/Register')
      }
    },
    {
      path: '/register-done',
      name: 'register-done',
      components: {
        navigation: NavigationAuth,
        default: () => import(/* webpackChunkName: "register" */ './views/auth/RegisterDone')
      }
    },
    {
      path: '/login',
      name: 'login',
      components: {
        navigation: NavigationAuth,
        default: () => import(/* webpackChunkName: "login" */ './views/auth/Login')
      }
    },
    {
      path: '/loginCallback',
      name: 'loginCallback',
      components: {
        navigation: NavigationAuth,
        default: () => import(/* webpackChunkName: "login" */ './views/auth/LoginCallback')
      }
    },
    {
      path: '/',
      name: 'home',
      components: {
        navigation: NavigationDefault,
        default: () => import(/* webpackChunkName: "about" */ './views/Home')
      },
      beforeEnter: requireAuth
    },
    {
      path: '/profile',
      name: 'profile',
      components: {
        navigation: NavigationDefault,
        default: () => import(/* webpackChunkName: "about" */ './views/Profile')
      },
      beforeEnter: requireAuth
    },
    {
      path: '/camps',
      name: 'camps',
      components: {
        navigation: NavigationDefault,
        default: () => import(/* webpackChunkName: "camps" */ './views/Camps')
      },
      beforeEnter: requireAuth
    },
    {
      path: '/camps/create',
      name: 'camps/create',
      components: {
        navigation: NavigationDefault,
        default: () => import(/* webpackChunkName: "camps" */ './views/CampCreate')
      },
      beforeEnter: requireAuth
    },
    {
      path: '/camps/invitation/:inviteKey',
      name: 'campInvitation',
      components: {
        navigation: NavigationAuth,
        default: () => import(/* webpackChunkName: "login" */ './views/camp/Invitation')
      },
      props: {
        default: route => {
          return {
            campCollaboration: campCollaborationsFromInviteKey(route.params.inviteKey)
          }
        }
      }
    },
    {
      path: '/camps/:campId/:campTitle?',
      components: {
        navigation: NavigationCamp,
        default: () => import(/* webpackChunkName: "camp" */ './views/camp/Camp'),
        aside: () => import(/* webpackChunkName: "periods" */ './views/camp/SideBarPeriods')
      },
      beforeEnter: all([requireAuth, requireCamp]),
      props: {
        navigation: route => ({ camp: campFromRoute(route) }),
        default: route => ({ camp: campFromRoute(route), period: periodFromRoute(route) }),
        aside: route => ({ camp: campFromRoute(route), period: periodFromRoute(route) })
      },
      children: [
        {
          path: 'collaborators',
          name: 'camp/collaborators',
          component: () => import(/* webpackChunkName: "campCollaborators" */ './views/camp/Collaborators')
        },
        {
          path: 'admin',
          name: 'camp/admin',
          component: () => import(/* webpackChunkName: "campAdmin" */ './views/camp/Admin')
        },
        {
          path: 'period/:periodId/:periodTitle?',
          name: 'camp/period',
          component: () => import(/* webpackChunkName: "campProgram" */ './views/camp/CampProgram'),
          beforeEnter: requirePeriod
        },
        {
          path: 'print',
          name: 'camp/print',
          component: () => import(/* webpackChunkName: "campPrint" */ './views/camp/Print')
        },
        {
          path: 'story',
          name: 'camp/story',
          component: () => import(/* webpackChunkName: "campPrint" */ './views/camp/Story')
        },
        {
          path: '',
          name: 'camp/program',
          async beforeEnter (to, from, next) {
            const period = await firstFuturePeriod(to)
            if (period) {
              await period.camp()._meta.load
              next(periodRoute(period))
            } else {
              const camp = await apiStore.get().camps({ campId: to.params.campId })
              next(campRoute(camp, 'admin'))
            }
          }
        }
      ]
    },
    {
      path: '/camps/:campId/:campTitle/activities/:scheduleEntryId/:activityName?',
      name: 'activity',
      components: {
        navigation: NavigationCamp,
        default: () => import(/* webpackChunkName: "activity" */ './views/activity/Activity'),
        aside: () => import(/* webpackChunkName: "day" */ './views/activity/SideBarProgram')
      },
      beforeEnter: requireAuth,
      props: {
        navigation: route => ({ camp: campFromRoute(route) }),
        default: route => ({ scheduleEntry: scheduleEntryFromRoute(route) }),
        aside: route => ({ day: dayFromScheduleEntryInRoute(route) })
      }
    }
  ]
})

function evaluateGuards (guards, to, from, next) {
  const guardsLeft = guards.slice(0)
  const nextGuard = guardsLeft.shift()

  if (nextGuard === undefined) {
    next()
    return
  }

  nextGuard(to, from, nextArg => {
    if (nextArg === undefined) {
      evaluateGuards(guardsLeft, to, from, next)
      return
    }
    next(nextArg)
  })
}

function all (guards) {
  return (to, from, next) => evaluateGuards(guards, to, from, next)
}

function requireAuth (to, from, next) {
  refreshLoginStatus(false).then(loggedIn => {
    if (loggedIn) {
      next()
    } else {
      next({ name: 'login', query: to.path === '/' ? {} : { redirect: to.fullPath } })
    }
  })
}

async function requireCamp (to, from, next) {
  await campFromRoute(to).call({ api: { get: apiStore.get } })._meta.load.then(() => {
    next()
  }).catch(() => {
    next({ name: 'home' })
  })
}

async function requirePeriod (to, from, next) {
  await periodFromRoute(to).call({ api: { get: apiStore.get } })._meta.load.then(() => {
    next()
  }).catch(() => {
    next(campRoute(campFromRoute(to).call({ api: { get: apiStore.get } })))
  })
}

export function campFromRoute (route) {
  return function () {
    return this.api.get().camps({ campId: route.params.campId })
  }
}

export function campCollaborationsFromInviteKey (inviteKey) {
  return function () {
    return this.api.get().invitation({ action: 'find', inviteKey: inviteKey })
  }
}

export function periodFromRoute (route) {
  return function () {
    return this.api.get().periods({ periodId: route.params.periodId })
  }
}

function scheduleEntryFromRoute (route) {
  return function () {
    return this.api.get().scheduleEntries({ scheduleEntryId: route.params.scheduleEntryId })
  }
}

function dayFromScheduleEntryInRoute (route) {
  return function () {
    return this.api.get().scheduleEntries({ scheduleEntryId: route.params.scheduleEntryId }).day()
  }
}

export function campRoute (camp, subroute) {
  if (camp._meta.loading) return {}
  const routeName = subroute ? 'camp/' + subroute : 'camp/program'
  return { name: routeName, params: { campId: camp.id, campTitle: slugify(camp.title) } }
}

export function loginRoute (redirectTo) {
  return { path: '/login', query: { redirect: redirectTo } }
}

export function periodRoute (period) {
  const camp = period.camp()
  if (camp._meta.loading || period._meta.loading) return {}
  return {
    name: 'camp/period',
    params: {
      campId: camp.id,
      campTitle: slugify(camp.title),
      periodId: period.id,
      periodTitle: slugify(period.description)
    }
  }
}

export function scheduleEntryRoute (camp, scheduleEntry) {
  if (camp._meta.loading || scheduleEntry._meta.loading || scheduleEntry.activity()._meta.loading) return {}
  return {
    name: 'activity',
    params: {
      campId: camp.id,
      campTitle: slugify(camp.title),
      scheduleEntryId: scheduleEntry.id,
      activityName: slugify(scheduleEntry.activity().title)
    }
  }
}

async function firstFuturePeriod (route) {
  const periods = await apiStore.get().camps({ campId: route.params.campId }).periods()._meta.load
  // Return the first period that hasn't ended, or if no such period exists, return the first period
  return periods.items.find(period => new Date(period.end) >= new Date()) || periods.items.find(_ => true)
}
