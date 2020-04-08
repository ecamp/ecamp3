class IgnoreAnnoyingWarnMessagesPlugin {
  install (Vue, options) {
    const ignoreWarnMessage = 'The .native modifier for v-on is only valid on components but it was used on'
    Vue.config.warnHandler = function (msg, vm, trace) {
      // `trace` is the component hierarchy trace
      if (msg.startsWith(ignoreWarnMessage)) {
        msg = null
        vm = null
        trace = null
      }
    }
  }
}

export default new IgnoreAnnoyingWarnMessagesPlugin()
