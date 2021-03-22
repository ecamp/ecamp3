export const contentNodeMixin = {
  props: {
    contentNode: { type: Object, required: true },
    layoutMode: { type: Boolean, required: true }
  },
  computed: {
    camp () {
      return this.contentNode.root().owner().camp()
    }
  }
}
