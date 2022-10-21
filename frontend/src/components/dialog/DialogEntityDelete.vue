<template>
  <dialog-form
    v-model="showDialog"
    :icon="icon"
    :title="$tc('components.dialog.dialogEntityDelete.title')"
    :error="error"
    :submit-action="del"
    :submit-enabled="submitEnabled && !$slots.error"
    submit-label="global.button.delete"
    submit-color="error"
    :submit-icon="icon"
    cancel-icon=""
    :cancel-action="close"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <slot>{{ $tc('components.dialog.dialogEntityDelete.warningText') }}</slot>
    <template v-if="$slots.error || error" #error>
      <slot name="error">
        {{ error }}
      </slot>
    </template>
  </dialog-form>
</template>

<script>
import DialogForm from './DialogForm.vue'
import DialogBase from './DialogBase.vue'

export default {
  name: 'DialogEntityDelete',
  components: { DialogForm },
  extends: DialogBase,
  props: {
    entity: { type: Object, required: true },
    submitEnabled: { type: Boolean, required: false, default: true },
    icon: { type: String, required: false, default: 'mdi-delete' },
  },
  created() {
    this.entityUri = this.entity._meta.self
  },
}
</script>

<style scoped></style>
