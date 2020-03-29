import { VTextField } from 'vuetify/lib'

export default {
  name: 'ETextField',
  extends: VTextField,
  props: {
    filled: {
      type: Boolean,
      default: true
    },
    hideDetails: {
      type: String,
      default: 'auto'
    },
    noMargin: {
      type: Boolean,
      default: false,
      required: false
    }
  },
  computed: {
    classes () {
      return {
        ...VTextField.options.computed.classes.call(this),
        'my-4': !this.noMargin
      }
    }
  }
}
