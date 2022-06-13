<script>
import { VTextField } from 'vuetify/lib'
import TiptapEditor from './TiptapEditor.vue'

export default {
  name: 'VTiptapEditor',
  components: {
    TiptapEditor,
  },
  extends: VTextField,
  props: {
    withExtensions: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    genInput() {
      const listeners = Object.assign({}, this.listeners$)
      return this.$createElement(TiptapEditor, {
        attrs: {
          ...this.attrs$,
          id: this.computedId,
        },
        props: {
          value: this.value,
          placeholder: this.placeholder,
          withExtensions: this.withExtensions,
          editable: !this.readonly,
        },
        on: Object.assign(listeners, {
          blur: this.onBlur,
          focus: this.onFocus,
          // input: this.onInput,
          mousedown: this.onMouseDown,
          mouseup: this.onMouseUp,
        }),
        ref: 'input',
      })
    },

    onBlur(e) {
      VTextField.options.methods.onBlur.call(this, e)
    },
    onFocus(e) {
      VTextField.options.methods.onFocus.call(this, e)

      if (!this.isFocused) {
        this.isFocused = true
        e && this.$emit('focus', e)
      }
    },
    onMouseDown(e) {
      if (e.target === this.$refs.input) {
        VTextField.options.methods.onMouseDown.call(this, e)
      }
    },
    onMouseUp(e) {
      if (e.target === this.$refs.input) {
        VTextField.options.methods.onMouseDown.call(this, e)
      }
    },
  },
}
</script>

<style scoped>
div.v-text-field--solo div.v-input__slot {
  align-items: normal;
}

div.v-text-field__slot {
  align-items: normal;
}

.v-text-field.v-text-field--box.v-text-field--single-line:not(.v-input--dense) .editor,
.v-text-field.v-text-field--box.v-text-field--outlined:not(.v-input--dense) .editor,
.v-text-field.v-text-field--enclosed.v-text-field--single-line:not(.v-input--dense)
  .editor,
.v-text-field.v-text-field--enclosed.v-text-field--outlined:not(.v-input--dense) .editor {
  margin-top: 10px;
}
</style>
