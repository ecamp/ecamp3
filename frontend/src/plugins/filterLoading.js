class FilterLoadingPlugin {
  install (Vue) {
    Vue.filter(
      'loading',
      function (
        value,
        loadingState,
        isLoading = (v) => typeof v === 'function' && v.loading
      ) {
        if (typeof value === 'function' && !value.loading) {
          // Wrap the function that is passed into the | loading filter
          return (v, ...args) => (isLoading(v) ? loadingState : value(v, ...args))
        }
        return isLoading(value) ? loadingState : value
      }
    )
  }
}

export default new FilterLoadingPlugin()
