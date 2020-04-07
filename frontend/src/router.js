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
  base: process.env.BASE_URL,
  routes: [
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
      path: '/camps/:campId/:campTitle?',
      components: {
        navigation: NavigationCamp,
        default: () => import(/* webpackChunkName: "camp" */ './views/camp/Camp'),
        aside: () => import(/* webpackChunkName: "periods" */ './views/camp/SideBarPeriods')
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
      path: '/camps/:campId/:campTitle/events/:eventInstanceId/:eventName?',
      name: 'event',
      components: {
        navigation: NavigationCamp,
        default: () => import(/* webpackChunkName: "event" */ './views/event/Event'),
        aside: () => import(/* webpackChunkName: "day" */ './views/event/SideBarProgram')
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

export function campFromRoute (route) {
  return function () {
    return this.api.get().camps({ camp_id: route.params.campId })
  }
}

function eventInstanceFromRoute (route) {
  return function () {
    return this.api.get().eventInstances({ event_instance_id: route.params.eventInstanceId })
  }
}

function dayFromEventInstanceInRoute (route) {
  return function () {
    return this.api.get().eventInstances({ event_instance_id: route.params.eventInstanceId }).day()
  }
}

export function campRoute (camp, subroute) {
  if (camp._meta.loading) return {}
  const routeName = subroute ? 'camp/' + subroute : 'camp/program'
  return { name: routeName, params: { campId: camp.id, campTitle: slugify(camp.title) } }
}

export function eventInstanceRoute (camp, eventInstance) {
  if (camp._meta.loading || eventInstance._meta.loading || eventInstance.event()._meta.loading) return {}
  return {
    name: 'event',
    params: { campId: camp.id, campTitle: slugify(camp.title), eventInstanceId: eventInstance.id, eventName: slugify(eventInstance.event().title) }
  }
}
