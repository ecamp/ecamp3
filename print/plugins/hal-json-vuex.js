import Vue from 'vue'
import HalJsonVuex from 'hal-json-vuex'

export default function ({ store, $axios }, nuxtInject) {
  // Avoid re-installing the plugin on the server side on every request
  if (!Vue.$api) {
    Vue.use(HalJsonVuex(store, $axios, { forceRequestedSelfLink: true, nuxtInject }))
  }
}
