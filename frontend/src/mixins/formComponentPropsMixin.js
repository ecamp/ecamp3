export const formComponentPropsMixin = {
  inject: {
    entityName: { default: null },
  },
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
      type: [String, Boolean],
      default: 'auto',
    },

    // set classes on input
    inputClass: {
      type: String,
      default: '',
      required: false,
    },

    // used as field name for validation
    name: {
      type: String,
      required: false,
      default: null,
    },

    // set the label which is displayed to the user
    label: {
      type: String,
      required: false,
      default: undefined,
    },

    // error messages from outside which should be displayed on the component
    errorMessages: {
      type: Array,
      required: false,
      default: () => [],
    },
  },
  computed: {
    labelOrEntityFieldLabel() {
      if (this.label !== undefined) {
        return this.label
      }
      if (!this.entityName || !this.name) {
        return null
      }
      return this.$t(`entity.${this.entityName}.fields.${this.name}`)
    },
  },
}
