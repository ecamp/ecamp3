export const campRoleMixin = {
  computed: {
    isContributor() {
      return this.isMember || this.isManager
    },
    isGuest() {
      return this.role === 'guest'
    },
    isManager() {
      return this.role === 'manager'
    },
    isMember() {
      return this.role === 'member'
    },
    role() {
      const currentUserLink = this.$store.state.auth.user._meta.self
      const result = this._campCollaborations
        .filter((coll) => typeof coll.user === 'function')
        .find((coll) => coll.user()._meta.self === currentUserLink)
      return result?.role
    },
    _campCollaborations() {
      const campCollaborations = this._camp?.campCollaborations()
      return campCollaborations?.items
    },
    _camp() {
      if (typeof this.camp === 'function') {
        return this.camp()
      }
      return this.camp
    },
  },
  mounted() {
    if (typeof this.camp !== 'object' && typeof this.camp !== 'function') {
      throw new Error(
        'User of the campRoleMixin must expose a camp as object proxy or function'
      )
    }
  },
}
