<script>
import { h, reactive, toRefs, toRef, computed } from 'vue'
import { VField } from 'vuetify/components'
import TiptapEditor from './TiptapEditor.vue'

export default {
  name: 'VTiptapEditor',
  components: {
    TiptapEditor,
  },
  extends: VField,
  props: {
    withExtensions: {
      type: Boolean,
      default: false,
    },
    modelValue: {
      type: String,
      default: '',
    },
    placeholder: {
      type: String,
      default: '',
    },
    readonly: {
      type: Boolean,
      default: false,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['update:modelValue'],
  setup(props, ctx) {
    const readonlyRef = toRef(() => props.readonly)
    const disabledRef = toRef(() => props.disabled)
    const tiptap = () =>
      h(
        TiptapEditor,
        reactive({
          ...toRefs(props),
          ...ctx.attrs,
          onTiptapUpdate: (value) => ctx.emit('update:modelValue', value),
          editable: computed(() => !readonlyRef.value && !disabledRef.value),
        }),
        ctx.slots
      )
    return VField.setup(
      reactive({
        ...toRefs(props),
        ...ctx.attrs,
        loading: toRef(() => props.loading),
      }),
      { ...ctx, slots: { default: tiptap, ...ctx.slots } }
    )
  },
}
</script>

<style scoped>
div.v-text-field--solo div.v-input__slot {
  align-items: normal;
}

div.v-text-field__slot {
  align-items: normal;
  width: 100%;
}

.v-field.v-field--variant-outlined {
  padding: 10px 12px;
}

.v-field :deep(.editor) {
  align-self: stretch;
}

.v-field :deep(.editor),
.v-field :deep(.editor__content) {
  display: flex;
  flex-direction: column;
}

.v-field :deep(.ProseMirror) {
  flex: 1;
}
</style>
