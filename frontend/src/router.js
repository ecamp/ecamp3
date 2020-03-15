import Vue from 'vue'
import Router from 'vue-router'
import slugify from 'slugify'
import { refreshLoginStatus } from '@/auth'

Vue.use(Router)

export default new Router({
  mode: 'history',
  base: process.env.BASE_URL,
  routes: [
    {
      path: '/register',
      name: 'register',
      components: {
        topbar: () => import(/* webpackChunkName: "navigation" */ './views/auth/TopBar'),
        default: () => import(/* webpackChunkName: "register" */ './views/auth/Register')
      }
    },
    {
      path: '/register-done',
      name: 'register-done',
      components: {
        topbar: () => import(/* webpackChunkName: "navigation" */ './views/auth/TopBar'),
        default: () => import(/* webpackChunkName: "register" */ './views/auth/RegisterDone')
      }
    },
    {
      path: '/login',
      name: 'login',
      components: {
        topbar: () => import(/* webpackChunkName: "navigation" */ './views/auth/TopBar'),
        default: () => import(/* webpackChunkName: "login" */ './views/auth/Login')
      }
    },
    {
      path: '/loginCallback',
      name: 'loginCallback',
      components: {
        topbar: () => import(/* webpackChunkName: "navigation" */ './views/auth/TopBar'),
        default: () => import(/* webpackChunkName: "login" */ './views/auth/LoginCallback')
      }
    },
    {
      path: '/',
      name: 'home',
      components: {
        topbar: () => import(/* webpackChunkName: "navigation" */ './views/TopBar'),
        default: () => import(/* webpackChunkName: "about" */ './views/Home'),
        bottombar: () => import(/* webpackChunkName: "navigation" */ './views/BottomBar')
      },
      beforeEnter: requireAuth
    },
    {
      path: '/profile',
      name: 'profile',
      components: {
        topbar: () => import(/* webpackChunkName: "navigation" */ './views/TopBar'),
        default: () => import(/* webpackChunkName: "about" */ './views/Profile'),
        bottombar: () => import(/* webpackChunkName: "navigation" */ './views/BottomBar')
      },
      beforeEnter: requireAuth
    },
    {
      path: '/camps',
      name: 'camps',
      components: {
        topbar: () => import(/* webpackChunkName: "navigation" */ './views/TopBar'),
        default: () => import(/* webpackChunkName: "camps" */ './views/Camps'),
        bottombar: () => import(/* webpackChunkName: "navigation" */ './views/BottomBar')
      },
      beforeEnter: requireAuth
    },
    {
      path: '/camps/:campId/:campTitle?',
      components: {
        topbar: () => import(/* webpackChunkName: "navigation" */ './views/camp/TopBar'),
        default: () => import(/* webpackChunkName: "camp" */ './views/camp/Camp'),
        aside: () => import(/* webpackChunkName: "periods" */ './views/camp/Periods'),
        bottombar: () => import(/* webpackChunkName: "navigation" */ './views/camp/BottomBar')
      },
      beforeEnter: requireAuth,
      props: {
        default: route => ({ camp: campFromRoute(route) }),
        aside: route => ({ camp: campFromRoute(route) })
      },
      children: [
        {
          path: 'collaborators',
          name: 'camp/collaborators',
          component: () => import(/* webpackChunkName: "campCollaborators" */ './views/camp/Collaborators.vue')
        },
        {
          path: 'overview',
          name: 'camp/overview',
          component: () => import(/* webpackChunkName: "campPicasso" */ './views/camp/CampOverview'),
          beforeEnter: parseBooleanInQuery
        },
        {
          path: 'admin',
          name: 'camp/admin',
          component: () => import(/* webpackChunkName: "campAdmin" */ './views/camp/Admin')
        }
      ]
    },
    {
      path: '/camps/:campId/:campTitle/events/:eventInstanceId/:eventName?',
      name: 'event',
      components: {
        topbar: () => import(/* webpackChunkName: "navigation" */ './views/camp/TopBar'),
        default: () => import(/* webpackChunkName: "event" */ './views/event/Event'),
        aside: () => import(/* webpackChunkName: "day" */ './views/event/DayOverview'),
        bottombar: () => import(/* webpackChunkName: "navigation" */ './views/camp/BottomBar')
      },
      beforeEnter: requireAuth,
      props: {
        default: route => ({ eventInstance: eventInstanceFromRoute(route) }),
        aside: route => ({ day: dayFromEventInstanceInRoute(route) })
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

function parseBooleanInQuery (to, from, next) {
  for (const [key, val] of Object.entries(to.query)) {
    if (val === null || val === 'true') {
      to.query[key] = true
    } else if (val === 'false') {
      to.query[key] = false
    }
  }
  next()
}

export function campFromRoute (route) {
  return function () {
    return this.api.get('/camp/' + route.params.campId)
  }
}

function eventInstanceFromRoute (route) {
  return function () {
    return this.api.get('/event-instance/' + route.params.eventInstanceId)
  }
}

function dayFromEventInstanceInRoute (route) {
  return function () {
    return this.api.get('/event-instance/' + route.params.eventInstanceId).day()
  }
}

export function campRoute (camp, subroute) {
  if (camp._meta.loading) return {}
  const routeName = subroute ? 'camp/' + subroute : 'camp/overview'
  return { name: routeName, params: { campId: camp.id, campTitle: slugify(camp.title) } }
}

export function eventInstanceRoute (camp, eventInstance) {
  if (camp._meta.loading || eventInstance._meta.loading || eventInstance.event()._meta.loading) return {}
  return {
    name: 'event',
    params: { campId: camp.id, campTitle: slugify(camp.title), eventInstanceId: eventInstance.id, eventName: slugify(eventInstance.event().title) }
  }
}
