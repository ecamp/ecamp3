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

    /**
     * used as field path for validation
     * and together with entityName as label (if no override label is provided)
     */
    path: {
      type: String,
      required: false,
      default: null,
    },

    /**
     * override the automatic entity field label
     */
    label: {
      type: String,
      required: false,
      default: undefined,
    },

    /**
     * override the automatic validation field name
     */
    validationLabelOverride: {
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
      if (!this.entityName || !this.path) {
        return null
      }
      return this.$t(`entity.${this.entityName}.fields.${this.path}`)
    },
    validationLabel() {
      if (this.validationLabelOverride !== undefined) {
        return this.validationLabelOverride
      }
      if (this.label) {
        return this.label
      }
      return this.$t(`entity.${this.entityName}.fields.${this.path}`)
    },
  },
}
