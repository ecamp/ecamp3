import { VTextarea } from 'vuetify/lib'

export default {
  name: 'ETextarea',
  extends: VTextarea,
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
        ...VTextarea.options.computed.classes.call(this),
        'my-4': !this.noMargin
      }
    }
  }
}
