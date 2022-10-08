import { throttle } from 'lodash'

// Use dynamic import for large dependency which is only used on very few pages
const { passwordStrength } = await import('@/plugins/passwordStrength.js')

export const passwordStrengthMixin = {
  data: () => ({
    passwordStrength: 0,
    passwordStrengthColor: 'green',
  }),
  methods: {
    async strength(password, lang = this.$store.state.lang.language.substr(0, 2)) {
      if (password.length === 0) {
        this.passwordStrength = 0
      } else {
        const strengthInfo = await passwordStrength(password, lang)
        this.passwordStrength = (1 + strengthInfo.score) * 20
      }
      return this.passwordStrength
    },
    async strengthColor(password) {
      const strength = await this.strength(password)
      this.passwordStrengthColor = 'green'
      if (strength <= 75) this.passwordStrengthColor = 'yellow'
      if (strength <= 50) this.passwordStrengthColor = 'orange'
      if (strength <= 25) this.passwordStrengthColor = 'red'
      return this.passwordStrengthColor
    },
    debouncedPasswordStrengthCheck: throttle(function (password) {
      this.strengthColor(password).then()
    }, 250),
  },
}
