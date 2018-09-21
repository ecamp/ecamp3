import Vue from 'vue'
import CampDetails from './components/camp-details'

const vueApps = {
  'camp-details': CampDetails
}

for (let id in vueApps) {
  if (!vueApps.hasOwnProperty(id)) continue
  if (document.getElementById(id)) {
    new Vue({ // eslint-disable-line no-new
      el: '#' + id,
      render (h) {
        return h(vueApps[id], { props: this.$el.dataset })
      }
    })
  }
}
