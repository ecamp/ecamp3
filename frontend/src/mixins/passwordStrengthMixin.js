import { passwordStrength } from 'check-password-strength'

export const passwordStrengthMixin = {
  methods: {
    strengthInfo(password) {
      return passwordStrength(password)
    },
    strength(password) {
      const strengthInfo = this.strengthInfo(password)
      if (strengthInfo.length === 0) return 0
      return (1 + strengthInfo.id) * 25
    },
    strengthColor(password) {
      const strength = this.strength(password)
      if (strength <= 25) return 'red'
      if (strength <= 50) return 'orange'
      if (strength <= 75) return 'yellow'
      return 'green'
    },
  }
}
