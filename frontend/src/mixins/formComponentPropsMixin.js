export const formComponentPropsMixin = {
  props: {

    // vuetify property hideDetails
    filled: {
      type: Boolean,
      default: true
    },

    // vuetify property hideDetails
    hideDetails: {
      type: String,
      default: 'auto'
    },

    // vertical margin
    my: {
      type: Number,
      default: 4,
      required: false
    },

    // used as field name for validation and as label (if no fallback label is provided)
    name: {
      type: String,
      required: false,
      default: null
    },

    // fallback label; name is used as label if not provided
    label: {
      type: String,
      required: false,
      default: null
    },

    // ID for vee-validation
    veeId: {
      type: String,
      required: false,
      default: null
    },

    // rules for vee-validation
    veeRules: {
      type: String,
      required: false,
      default: null
    }
  }
}
