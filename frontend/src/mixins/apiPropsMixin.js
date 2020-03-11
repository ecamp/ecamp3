export const apiPropsMixin = {
  inheritAttrs: false,

  props: {
    value: { required: true },

    /* field name and URI for saving back to API */
    fieldname: { type: String, required: true },
    uri: { type: String, required: true },

    /* overrideDirty=true will reset the input if 'value' changes, even if the input is dirty. Use with caution. */
    overrideDirty: { type: Boolean, default: false, required: false },

    /* enable/disable edit mode */
    readonly: { type: Boolean, required: false, default: false },
    disabled: { type: Boolean, required: false, default: false },

    /* enable/disable auto save */
    autoSave: { type: Boolean, default: true, required: false },
    autoSaveDelay: { type: Number, default: 800, required: false },

    /* Validation criteria */
    required: { type: Boolean, default: false, required: false }
  }

}
