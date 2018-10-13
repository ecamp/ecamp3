import 'regenerator-runtime/runtime'
import Vue from 'vue'
import Router from 'vue-router'

// Add vue plugins here
Vue.use(Router)

// Add routes here
let router = new Router({
  mode: 'history',
  routes: [
    {
      path: '/group/:groupName/camp/:campName',
      component: () => import('@/components/camp-details'),
      props: true
    }
  ]
})

new Vue({ // eslint-disable-line no-new
  el: '#app',
  router,
  render (h) {
    let props = this.$el.dataset
    return h('router-view', { props })
  }
})
