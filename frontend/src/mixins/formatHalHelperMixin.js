export const FormatHalHelperMixin = {
  methods: {
    toId(uri) {
      return uri.substring(uri.lastIndexOf('/') + 1)
    },
    toUri(dataType, id) {
      return `/${dataType}/${id}`
    },
  },
}
