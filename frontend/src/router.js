import Vue from 'vue'
import Router from 'vue-router'
import slugify from 'slugify'
import { refreshLoginStatus } from '@/plugins/auth'

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
      path: '/camps/:campId/:campTitle?/period/:periodId/:periodTitle?',
      components: {
        navigation: NavigationCamp,
        default: () => import(/* webpackChunkName: "camp" */ './views/camp/Camp'),
        aside: () => import(/* webpackChunkName: "periods" */ './views/camp/SideBarPeriods')
      },
      beforeEnter: requireAuth,
      props: {
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
          path: 'program',
          name: 'camp/program',
          component: () => import(/* webpackChunkName: "campProgram" */ './views/camp/CampProgram')
        },
        {
          path: '',
          redirect: { name: 'camp/program' }
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
        default: route => ({ scheduleEntry: scheduleEntryFromRoute(route) }),
        aside: route => ({ day: dayFromScheduleEntryInRoute(route) })
      }
    }
  ]
})

function requireAuth (to, from, next) {
  refreshLoginStatus(false).then(loggedIn => {
    if (loggedIn) {
      next()
    } else {
      next({ name: 'login', query: to.path === '/' ? {} : { redirect: to.fullPath } })
    }
  })
}

export function campFromRoute (route) {
  return function () {
    return this.api.get().camps({ campId: route.params.campId })
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

export function scheduleEntryRoute (camp, scheduleEntry) {
  if (camp._meta.loading || scheduleEntry._meta.loading || scheduleEntry.activity()._meta.loading) return {}
  return {
    name: 'activity',
    params: { campId: camp.id, campTitle: slugify(camp.title), scheduleEntryId: scheduleEntry.id, activityName: slugify(scheduleEntry.activity().title) }
  }
}
