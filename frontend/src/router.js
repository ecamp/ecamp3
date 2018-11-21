import Vue from 'vue'
import Router from 'vue-router'

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
      component: () => import(/* webpackChunkName: "camps" */ './views/Camps.vue')
    },
    {
      path: '/group/:groupName/camp/:campId',
      component: () => import(/* webpackChunkName: "camp" */ './views/Camp.vue'),
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
    },
    {
      path: '/loginCallback',
      name: 'loginCallback',
      component: () => import(/* webpackChunkName: "login" */ './views/LoginCallback.vue')
    },
    {
      path: '/logout',
      name: 'logout',
      component: () => import(/* webpackChunkName: "logout" */ './views/Logout.vue')
    }
  ]
})
