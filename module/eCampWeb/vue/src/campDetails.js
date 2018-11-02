import 'regenerator-runtime/runtime'
import Vue from 'vue'
import Router from 'vue-router'
import CampDetailsContainer from '@/components/CampDetailsContainer'

// Add vue plugins here
Vue.use(Router)

// Add routes here
let router = new Router({
  routes: [
    {
      path: '/',
      component: () => import('@/components/CampDetails'),
      props: () => ({ infoUrl: window.location.pathname + '?route-match=true' })
    }
  ]
})

new Vue({ // eslint-disable-line no-new
  el: '#app',
  components: { CampDetailsContainer },
  router,
  render (h) {
    let props = this.$el.dataset
    return h('camp-details-container', { props })
  }
})
