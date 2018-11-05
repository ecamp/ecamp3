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
      path: '/group/:groupname/camps',
      name: 'camps',
      component: () => import(/* webpackChunkName: "camps" */ './views/Camps.vue')
    },
    {
      path: '/group/:groupname/camp/:campname',
      name: 'camp',
      component: () => import(/* webpackChunkName: "camp" */ './views/Camp.vue'),
      children: [
        {
          path: '',
          name: 'camp/basic',
          component: () => import(/* webpackChunkName: "campDetails" */ './components/camp/Basic.vue')
        },
        {
          path: 'periods',
          name: 'camp/periods',
          component: () => import(/* webpackChunkName: "campPeriods" */ './components/camp/Periods.vue')
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
