import HalJsonVuex from 'hal-json-vuex'

export default function (context, inject) {
  const api = new HalJsonVuex(context.store, context.$axios, {
    forceRequestedSelfLink: true,
  })

  inject('api', api)
}
