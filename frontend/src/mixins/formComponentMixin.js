export const formComponentMixin = {
  computed: {
    required() {
      return this.veeRules.split('|').includes('required')
    },
  },
}
