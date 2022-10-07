export const formComponentMixin = {
  props: {
    // ID for vee-validation
    veeId: {
      type: String,
      required: false,
      default: null,
    },

    // rules for vee-validation
    veeRules: {
      type: [String, Object],
      required: false,
      default: '',
    },
  },
  computed: {
    required() {
      if ('object' === typeof this.veeRules) {
        return Object.keys(this.veeRules).includes('required')
      }
      return this.veeRules.split('|').includes('required')
    },
  },
}
