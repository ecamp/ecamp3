/**
 * Disables display of console warn messages related to .native event bindings
 *
 * See also here https://github.com/vuejs/vue/issues/10939
 * and here https://github.com/vuetifyjs/vuetify/issues/9999
 */
class IgnoreNativeBindingWarnMessagesPlugin {
  install(Vue) {
    const ignoreWarnMessage =
      'The .native modifier for v-on is only valid on components but it was used on'
    Vue.config.warnHandler = function (msg, vm, trace) {
      // `trace` is the component hierarchy trace
      if (msg.startsWith(ignoreWarnMessage)) {
        msg = null
        vm = null
        trace = null
      } else {
        console.error(`[Vue warn]: ${msg}${trace}`)
      }
    }
  }
}

export default new IgnoreNativeBindingWarnMessagesPlugin()
