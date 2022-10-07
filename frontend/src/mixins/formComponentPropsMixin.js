export const formComponentPropsMixin = {
  props: {
    id: {
      type: String,
      required: false,
      default: null,
    },

    // vuetify property hideDetails
    filled: {
      type: Boolean,
      default: true,
    },

    // vuetify property hideDetails
    hideDetails: {
      type: String,
      default: 'auto',
    },

    // set classes on input
    inputClass: {
      type: String,
      default: '',
      required: false,
    },

    // used as field name for validation and as label (if no fallback label is provided)
    name: {
      type: String,
      required: false,
      default: null,
    },

    // fallback label; name is used as label if not provided
    label: {
      type: String,
      required: false,
      default: null,
    },

    // error messages from outside which should be displayed on the component
    errorMessages: {
      type: Array,
      required: false,
      default: () => [],
    },
  },
}
