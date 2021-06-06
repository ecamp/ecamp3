export const campRoleMixin = {
  computed: {
    isContributor () {
      return this.isMember || this.isManager
    },
    isGuest () {
      return this.camp?.role === 'guest'
    },
    isManager () {
      return this.camp?.role === 'manager'
    },
    isMember () {
      return this.camp?.role === 'member'
    }
  },
  mounted () {
    if (typeof this.camp !== 'object') {
      throw new Error('User of the campRoleMixin must expose a method/proxy camp()')
    }
  }
}
