export const campRoleMixin = {
  provide() {
    return {
      isCollaborator: this.isCollaborator,
      isContributor: this.isContributor,
      isGuest: this.isGuest,
      isManager: this.isManager,
      isMember: this.isMember,
      isOutsider: this.isOutsider,
    }
  },
  computed: {
    isCollaborator() {
      return this.isGuest || this.isContributor
    },
    isContributor() {
      return this.isMember || this.isManager
    },
    isGuest() {
      return this._campRole === 'guest'
    },
    isManager() {
      return this._campRole === 'manager'
    },
    isMember() {
      return this._campRole === 'member'
    },
    isOutsider() {
      return this._campCollaborations.length > 0 && this._campRole === undefined
    },
    _campRole() {
      const currentUserLink = this.$store.getters.getLoggedInUser?._meta.self
      const campCollaborations = this._campCollaborations
      const result = campCollaborations
        .filter((coll) => !coll._meta.loading)
        .filter((coll) => typeof coll.user === 'function')
        .find((coll) => coll.user()._meta.self === currentUserLink)

      if (!result && campCollaborations.some((coll) => coll._meta.loading)) {
        return null
      }
      return result?.role
    },
    _campCollaborations() {
      if (!this.camp) return []
      if (typeof this.camp.campCollaborations !== 'function') {
        return []
      }
      return this._camp?.campCollaborations()?.items
    },
    _camp() {
      if (typeof this.camp === 'function') {
        return this.camp()
      }
      return this.camp
    },
  },
}
