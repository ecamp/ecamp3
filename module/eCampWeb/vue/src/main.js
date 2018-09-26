import Vue from 'vue'

const vueApps = {
  'camp-details': () => import('./components/camp-details')
}

for (let id in vueApps) {
  if (!vueApps.hasOwnProperty(id)) continue
  const element = document.getElementById(id)
  if (element) {
    new Vue({ // eslint-disable-line no-new
      el: element,
      render (h) {
        return h(vueApps[id], { props: element.dataset })
      }
    })
  }
}
