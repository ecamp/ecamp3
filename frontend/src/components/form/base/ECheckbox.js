import { VCheckbox } from 'vuetify/lib'

export default {
  name: 'ECheckbox',
  extends: VCheckbox,
  props: {
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
        ...VCheckbox.options.computed.classes.call(this),
        'my-4': !this.noMargin
      }
    }
  }
}
