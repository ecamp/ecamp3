import { VSelect } from 'vuetify/lib'

export default {
  name: 'ESelect',
  extends: VSelect,
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
        ...VSelect.options.computed.classes.call(this),
        'my-4': !this.noMargin
      }
    }
  }
}
