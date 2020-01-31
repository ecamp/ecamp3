import Vue from 'vue'
import Router from 'vue-router'
import slugify from 'slugify'

Vue.use(Router)

export default new Router({
  mode: 'history',
  base: process.env.BASE_URL,
  routes: [
    {
      path: '/register',
      name: 'register',
      meta: { layout: 'empty' },
      component: () => import(/* webpackChunkName: "register" */ './views/auth/Register.vue')
    },
    {
      path: '/register-done',
      name: 'register-done',
      meta: { layout: 'empty' },
      component: () => import(/* webpackChunkName: "register" */ './views/auth/RegisterDone.vue')
    },
    {
      path: '/login',
      name: 'login',
      meta: { layout: 'empty' },
      component: () => import(/* webpackChunkName: "login" */ './views/auth/Login.vue')
    },
    {
      path: '/loginCallback',
      name: 'loginCallback',
      component: () => import(/* webpackChunkName: "login" */ './views/auth/LoginCallback.vue')
    },
    {
      path: '/logout',
      name: 'logout',
      component: () => import(/* webpackChunkName: "logout" */ './views/auth/Logout.vue')
    },
    {
      path: '/',
      name: 'home',
      component: () => import(/* webpackChunkName: "about" */ './views/Home.vue'),
      beforeEnter: requireAuth
    },
    {
      path: '/camps',
      name: 'camps',
      components: {
        default: () => import(/* webpackChunkName: "camps" */ './views/Camps.vue')
      },
      beforeEnter: requireAuth
    },
    {
      path: '/camps/:campId/:campName?',
      components: {
        default: () => import(/* webpackChunkName: "camp" */ './views/Camp.vue'),
        aside: () => import(/* webpackChunkName: "camps" */ './views/Camps.vue')
      },
      beforeEnter: requireAuth,
      props: {
        default: route => ({ camp: campFromRoute(route) })
      },
      children: [
        {
          path: 'collaborators',
          name: 'camp/collaborators',
          component: () => import(/* webpackChunkName: "campCollaborators" */ './components/camp/Collaborators.vue'),
          meta: { layout: 'camp' }
        },
        {
          path: 'periods',
          name: 'camp/periods',
          component: () => import(/* webpackChunkName: "campPeriods" */ './components/camp/Periods.vue'),
          meta: { layout: 'camp' }
        },
        {
          path: 'picasso',
          name: 'camp/picasso',
          component: () => import(/* webpackChunkName: "campPicasso" */ './components/camp/Picasso.vue'),
          meta: { layout: 'camp' }
        },
        {
          path: '',
          name: 'camp',
          component: () => import(/* webpackChunkName: "campDetails" */ './components/camp/Basic.vue'),
          meta: { layout: 'camp' }
        }
      ]
    },
    {
      path: '/camps/:campId/:campName?/events/:eventId/:eventName?',
      name: 'event',
      components: {
        default: () => import(/* webpackChunkName: "event" */ './views/Event.vue'),
        aside: () => import(/* webpackChunkName: "day" */ './views/Day.vue')
      },
      beforeEnter: requireAuth,
      meta: { layout: 'camp' },
      props: {
        default: route => ({ event: eventFromRoute(route) }),
        aside: route => ({ camp: campFromRoute(route) })
      }
    }

  ]
})

function requireAuth (to, from, next) {
  Vue.auth.isLoggedIn().then(loggedIn => {
    if (loggedIn) {
      next()
    } else {
      next({ name: 'login', query: { redirect: to.fullPath } })
    }
  })
}

export function campFromRoute (route) {
  return function () {
    return this.api.get().camps().items.find(camp => camp.id === route.params.campId)
  }
}

function eventFromRoute (route) {
  return function () {
    const camp = (campFromRoute(route)).call(this)
    return camp.events().items.find(event => event.id === route.params.eventId)
  }
}

export function campRoute (camp) {
  return { name: 'camp', params: { campId: camp.id, campName: slugify(camp.title) } }
}

export function eventRoute (camp, eventInstance) {
  return { name: 'event', params: { campId: camp.id, campName: slugify(camp.title), eventId: eventInstance.event().id, eventName: slugify(eventInstance.event().title) } }
}
