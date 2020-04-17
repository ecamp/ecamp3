export const apiPropsMixin = {
  inheritAttrs: false,
  inject: {
    apiUri: { default: null }
  },
  props: {
    /* value is not required; by default value is read directly from api */
    value: { required: false, default: null },

    /* displayed label (also used in error messages) */
    label: { type: String, required: false },

    /* field name and URI for saving back to API */
    fieldname: { type: String, required: true },

    /* load devault value from apiObject (via ApiForm injection) */
    uri: {
      type: String,
      required: false,
      default () {
        if (this.apiUri === null) {
          throw new Error('ApiWrapper: `uri` not set on component; no ApiForm component found as parent for fallback')
        }
        return this.apiUri
      }
    },

    /* overrideDirty=true will reset the input if 'value' changes, even if the input is dirty. Use with caution. */
    overrideDirty: { type: Boolean, default: false, required: false },

    /* enable/disable edit mode */
    readonly: { type: Boolean, required: false, default: false },
    disabled: { type: Boolean, required: false, default: false },

    /* enable/disable auto save */
    autoSave: { type: Boolean, default: true, required: false },
    autoSaveDelay: { type: Number, default: 800, required: false },

    /* Validation criteria */
    required: { type: Boolean, default: false, required: false },

    /* Validation criteria (Vee-validate rule string) */
    validation: {
      type: String,
      required: false,
      default: null
    },

    /* Removes the margin (for inline fields) */
    noMargin: { type: Boolean, default: false, required: false }
  }

}
