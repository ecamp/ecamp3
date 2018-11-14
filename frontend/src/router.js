import Vue from 'vue'
import Router from 'vue-router'
import { isLoggedIn } from '@/auth'

Vue.use(Router)

export default new Router({
  mode: 'history',
  base: process.env.BASE_URL,
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import(/* webpackChunkName: "about" */ './views/Home.vue')
    },
    {
      path: '/group/:groupName/camps',
      name: 'camps',
      component: () => import(/* webpackChunkName: "camps" */ './views/Camps.vue'),
      beforeEnter: requireAuth
    },
    {
      path: '/group/:groupName/camp/:campId',
      component: () => import(/* webpackChunkName: "camp" */ './views/Camp.vue'),
      beforeEnter: requireAuth,
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
    },
    {
      path: '/login',
      name: 'login',
      component: () => import(/* webpackChunkName: "login" */ './views/Login.vue')
    }
  ]
})

function requireAuth (to, from, next) {
  if (isLoggedIn()) {
    next()
  } else {
    next({ name: 'login', query: { redirect: to.fullPath } })
  }
}
