import Vue from 'vue'
import Router from 'vue-router'

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
      component: () => import(/* webpackChunkName: "about" */ './views/Home.vue')
    },
    {
      path: '/group/:groupName/camps',
      name: 'camps',
      components: {
        aside: () => import(/* webpackChunkName: "camps" */ './views/Camps.vue')
      },
      beforeEnter: requireAuth
    },
    {
      path: '/group/:groupName/camp/:campId',
      components: {
        default: () => import(/* webpackChunkName: "camp" */ './views/Camp.vue'),
        aside: () => import(/* webpackChunkName: "camps" */ './views/Camps.vue'),
      }, beforeEnter: requireAuth,
      children: [
        {
          path: '',
          name: 'camp',
          component: () => import(/* webpackChunkName: "campDetails" */ './components/camp/Basic.vue'),
          props: true
        },
        {
          path: 'periods',
          name: 'camp/periods',
          component: () => import(/* webpackChunkName: "campPeriods" */ './components/camp/Periods.vue'),
          props: true
        }
      ]
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
