export const campRoleMixin = {
  computed: {
    isContributor () {
      return this.isMember || this.isManager
    },
    isGuest () {
      return this.role === 'guest'
    },
    isManager () {
      return this.role === 'manager'
    },
    isMember () {
      return this.role === 'member'
    },
    role () {
      if (typeof this.camp === 'function') {
        return this.camp()?.role
      }
      return this.camp?.role
    }
  },
  mounted () {
    if (typeof this.camp !== 'object' && typeof this.camp !== 'function') {
      throw new Error('User of the campRoleMixin must expose a camp as object proxy or function')
    }
  }
}
