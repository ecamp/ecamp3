export const apiPropsMixin = {
  inheritAttrs: false,
  inject: {
    apiUri: { default: null }
  },
  props: {
    /* value is not required; by default value is read directly from api */
    value: { required: false, default: null },

    /* field name and URI for saving back to API */
    // TODO: consider renaming to 'path', as this now allows to contain a nested json path
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

    /* control style */
    filled: {
      type: Boolean,
      default: false,
      required: false
    },
    outlined: {
      type: Boolean,
      default: true,
      required: false
    },
    dense: {
      type: Boolean,
      default: false,
      required: false
    }
  }

}
