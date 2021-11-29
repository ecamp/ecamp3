import Vue from 'vue'
import Router from 'vue-router'
import slugify from 'slugify'
import { isLoggedIn } from '@/plugins/auth'
import { apiStore } from '@/plugins/store'

Vue.use(Router)

const NavigationAuth = () => import(/* webpackChunkName: "navigationAuth" */ './views/auth/NavigationAuth.vue')
const NavigationDefault = () => import(/* webpackChunkName: "navigationDefault" */ './views/NavigationDefault.vue')
const NavigationCamp = () => import(/* webpackChunkName: "navigationCamp" */ './views/camp/NavigationCamp.vue')

/* istanbul ignore next */
export default new Router({
  mode: 'history',
  base: window.environment?.BASE_URL || '/',
  routes: [
    // Dev-Pages:
    {
      path: '/controls',
      name: 'controls',
      components: {
        navigation: NavigationDefault,
        default: () => import(/* webpackChunkName: "register" */ './views/dev/Controls.vue')
      }
    },

    {
      path: '/print',
      name: 'print',
      components: {
        default: () => import(/* webpackChunkName: "register" */ './views/dev/Print.vue')
      }
    },

    // Prod-Pages:
    {
      path: '/register',
      name: 'register',
      components: {
        navigation: NavigationAuth,
        default: () => import(/* webpackChunkName: "register" */ './views/auth/Register.vue')
      }
    },
    {
      path: '/register-done',
      name: 'register-done',
      components: {
        navigation: NavigationAuth,
        default: () => import(/* webpackChunkName: "register" */ './views/auth/RegisterDone.vue')
      }
    },
    {
      path: '/activate/:userId/:activationKey',
      name: 'activate',
      components: {
        navigation: NavigationAuth,
        default: () => import(/* webpackChunkName: "register" */ './views/auth/Activate.vue')
      },
      props: {
        default: route => {
          return {
            userId: route.params.userId,
            activationKey: route.params.activationKey
          }
        }
      }
    },
    {
      path: '/login',
      name: 'login',
      components: {
        navigation: NavigationAuth,
        default: () => import(/* webpackChunkName: "login" */ './views/auth/Login.vue')
      }
    },
    {
      path: '/loginCallback',
      name: 'loginCallback',
      components: {
        navigation: NavigationAuth,
        default: () => import(/* webpackChunkName: "login" */ './views/auth/LoginCallback.vue')
      }
    },
    {
      path: '/',
      name: 'home',
      components: {
        navigation: NavigationDefault,
        default: () => import(/* webpackChunkName: "about" */ './views/Home.vue')
      },
      beforeEnter: requireAuth
    },
    {
      path: '/profile',
      name: 'profile',
      components: {
        navigation: NavigationDefault,
        default: () => import(/* webpackChunkName: "about" */ './views/Profile.vue')
      },
      beforeEnter: requireAuth
    },
    {
      path: '/camps',
      name: 'camps',
      components: {
        navigation: NavigationDefault,
        default: () => import(/* webpackChunkName: "camps" */ './views/Camps.vue')
      },
      beforeEnter: requireAuth
    },
    {
      path: '/camps/create',
      name: 'camps/create',
      components: {
        navigation: NavigationDefault,
        default: () => import(/* webpackChunkName: "camps" */ './views/CampCreate.vue')
      },
      beforeEnter: requireAuth
    },
    {
      path: '/camps/invitation/:inviteKey',
      name: 'campInvitation',
      components: {
        navigation: NavigationAuth,
        default: () => import(/* webpackChunkName: "login" */ './views/camp/Invitation.vue')
      },
      props: {
        default: route => {
          return {
            invitation: invitationFromInviteKey(route.params.inviteKey)
          }
        }
      }
    },
    {
      path: '/camps/invitation/rejected',
      name: 'invitationRejected',
      components: {
        navigation: NavigationAuth,
        default: () => import(/* webpackChunkName: "login" */ './views/camp/InvitationRejected.vue')
      }
    },
    {
      path: '/camps/invitation/updateError',
      name: 'invitationUpdateError',
      components: {
        navigation: NavigationAuth,
        default: () => import(/* webpackChunkName: "login" */ './views/camp/InvitationUpdateError.vue')
      }
    },
    {
      path: '/camps/:campId/:campTitle?',
      components: {
        navigation: NavigationCamp,
        default: () => import(/* webpackChunkName: "camp" */ './views/camp/Camp.vue'),
        aside: () => import(/* webpackChunkName: "periods" */ './views/camp/SideBarPeriods.vue')
      },
      beforeEnter: all([requireAuth, requireCamp]),
      props: {
        navigation: route => ({ camp: campFromRoute(route) }),
        default: route => ({ camp: campFromRoute(route), period: periodFromRoute(route), layout: getContentLayout(route) }),
        aside: route => ({ camp: campFromRoute(route), period: periodFromRoute(route), show: showAside(route) })
      },
      children: [
        {
          path: 'collaborators',
          name: 'camp/collaborators',
          component: () => import(/* webpackChunkName: "campCollaborators" */ './views/camp/Collaborators.vue')
        },
        {
          path: 'admin',
          name: 'camp/admin',
          component: () => import(/* webpackChunkName: "campAdmin" */ './views/camp/Admin.vue')
        },
        {
          path: 'period/:periodId/:periodTitle?',
          name: 'camp/period',
          component: () => import(/* webpackChunkName: "campProgram" */ './views/camp/CampProgram.vue'),
          beforeEnter: requirePeriod
        },
        {
          path: 'print',
          component: () => import(/* webpackChunkName: "campPrint" */ './views/camp/Print.vue'),
          children: [
            {
              path: '',
              name: 'camp/print',
              component: () => import(/* webpackChunkName: "printCamp" */ './views/camp/PrintCamp.vue'),
              props: route => ({ camp: campFromRoute(route) })
            },
            {
              path: 'activity/:scheduleEntryId/:activityName?',
              name: 'camp/print/activity',
              component: () => import(/* webpackChunkName: "printActivity" */ './views/camp/PrintActivity.vue'),
              props: route => ({ scheduleEntry: scheduleEntryFromRoute(route) })
            },
            {
              path: 'period/:periodId/:periodTitle?',
              name: 'camp/print/period',
              component: () => import(/* webpackChunkName: "printActivity" */ './views/camp/PrintPeriod.vue'),
              props: route => ({ period: periodFromRoute(route) })
            }
          ]
        },
        {
          path: 'story',
          name: 'camp/story',
          component: () => import(/* webpackChunkName: "campPrint" */ './views/camp/Story.vue')
        },
        {
          path: 'material',
          name: 'camp/material',
          component: () => import(/* webpackChunkName: "campMaterial" */ './views/camp/Material.vue')
        },
        {
          path: 'program',
          name: 'camp/program',
          async beforeEnter (to, from, next) {
            const period = await firstFuturePeriod(to)
            if (period) {
              await period.camp()._meta.load
              next(periodRoute(period, to.query))
            } else {
              const camp = await apiStore.get().camps({ campId: to.params.campId })
              next(campRoute(camp, 'admin', to.query))
            }
          }
        },
        {
          path: '',
          name: 'camp/dashboard',
          component: () => import(/* webpackChungName: "camp" */ './views/camp/Dashboard.vue')
        }
      ]
    },
    {
      path: '/camps/:campId/:campTitle/category/:categoryId/:categoryName?',
      name: 'category',
      components: {
        navigation: NavigationCamp,
        default: () => import(/* webpackChunkName: "campCategory" */ './views/activity/Category.vue'),
        aside: () => import(/* webpackChunkName: "periods" */ './views/camp/SideBarPeriods.vue')
      },
      beforeEnter: requireAuth,
      props: {
        navigation: route => ({ camp: campFromRoute(route) }),
        default: route => ({ category: categoryFromRoute(route) }),
        aside: route => ({ camp: campFromRoute(route), period: () => null })
      }
    },
    {
      path: '/camps/:campId/:campTitle/activities/:scheduleEntryId/:activityName?',
      name: 'activity',
      components: {
        navigation: NavigationCamp,
        default: () => import(/* webpackChunkName: "activity" */ './views/activity/Activity.vue'),
        aside: () => import(/* webpackChunkName: "day" */ './views/activity/SideBarProgram.vue')
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
  if (isLoggedIn()) {
    next()
  } else {
    next({ name: 'login', query: to.path === '/' ? {} : { redirect: to.fullPath } })
  }
}

async function requireCamp (to, from, next) {
  await campFromRoute(to).call({ api: { get: apiStore.get } })._meta.load.then(() => {
    next({ query: to.query })
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
    // TODO add another decorator for EntrypointNormalizer and add our templated URIs there
    return this.api.get().camps({ id: route.params.campId })
  }
}

export function invitationFromInviteKey (inviteKey) {
  return function () {
    return this.api.get().invitations({ action: 'find', id: inviteKey })
  }
}

export function periodFromRoute (route) {
  return function () {
    return this.api.get().periods({ id: route.params.periodId })
  }
}

function scheduleEntryFromRoute (route) {
  return function () {
    return this.api.get().scheduleEntries({ id: route.params.scheduleEntryId })
  }
}

function categoryFromRoute (route) {
  return function () {
    const camp = this.api.get().camps({ id: route.params.campId })
    return camp.categories().items.find(c => c.id === route.params.categoryId)
  }
}

function getContentLayout (route) {
  switch (route.name) {
    case 'camp/period': return 'full'
    case 'camp/admin': return 'wide'
    default: return 'normal'
  }
}

function showAside (route) {
  return ['camp/period'].includes(route.name)
}

function dayFromScheduleEntryInRoute (route) {
  return function () {
    return this.api.get().scheduleEntries({ id: route.params.scheduleEntryId }).day()
  }
}

export function campRoute (camp, subroute = 'dashboard', query = {}) {
  if (camp._meta.loading) return {}
  return { name: 'camp/' + subroute, params: { campId: camp.id, campTitle: slugify(camp.title) }, query }
}

export function loginRoute (redirectTo) {
  return { path: '/login', query: { redirect: redirectTo } }
}

export function periodRoute (period, query = {}) {
  const camp = period.camp()
  if (camp._meta.loading || period._meta.loading) return {}
  return {
    name: 'camp/period',
    params: {
      campId: camp.id,
      campTitle: slugify(camp.title),
      periodId: period.id,
      periodTitle: slugify(period.description)
    },
    query
  }
}

export function scheduleEntryRoute (scheduleEntry, query = {}) {
  if (scheduleEntry._meta.loading || scheduleEntry.activity()._meta.loading) return {}

  const camp = scheduleEntry.activity().camp()

  // if (camp._meta.loading) return {}

  return {
    name: 'activity',
    params: {
      campId: camp.id,
      campTitle: slugify(camp.title),
      scheduleEntryId: scheduleEntry.id,
      activityName: slugify(scheduleEntry.activity().title)
    },
    query
  }
}

export function categoryRoute (camp, category, query = {}) {
  if (camp._meta.loading || category._meta.loading) return {}
  return {
    name: 'category',
    params: {
      campId: camp.id,
      campTitle: slugify(camp.title),
      categoryId: category.id,
      categoryName: slugify(category.name)
    },
    query
  }
}

async function firstFuturePeriod (route) {
  const periods = await apiStore.get().camps({ id: route.params.campId }).periods()._meta.load
  // Return the first period that hasn't ended, or if no such period exists, return the first period
  return periods.items.find(period => new Date(period.end) >= new Date()) || periods.items.find(_ => true)
}
