export default {
  computed: {
    camp () {
      return this.api.get().camps().items.find(camp => camp.id === this.$route.params.campId)
    }
  }
}
