<script>
export default {
  name: 'DialogUiBase',
  props: {
    value: { type: Boolean, required: true },

    submitAction: { type: Function, default: null, required: false },
    submitIcon: { type: String, default: 'mdi-send-variant', required: false },
    submitLabel: {
      type: String,
      default: function () {
        // return this.$tc('global.button.submit')
      },
      required: false,
    },
    submitColor: { type: String, default: 'primary', required: false },
    submitEnabled: { type: Boolean, default: true, required: false },

    cancelAction: { type: Function, default: null, required: false },
    cancelIcon: { type: String, default: 'mdi-window-close', required: false },
    cancelLabel: {
      type: String,
      default: function () {
        // return this.$tc('global.button.cancel')
      },
      required: false,
    },
    cancelColor: { type: String, default: 'secondary', required: false },
    cancelEnabled: { type: Boolean, default: true, required: false },
    cancelVisible: { type: Boolean, default: true, required: false },

    savingOverride: { type: Boolean, default: false, required: false },

    loading: { type: Boolean, default: false, required: false },
    error: { type: [Object, String, Error], default: null, required: false },
  },
  data() {
    return {
      isSaving: false,
    }
  },
}
</script>
