<template>
  <dialog-form
    v-model="showDialog"
    :icon="icon"
    :title="dialogTitle || $tc('components.dialog.dialogEntityDelete.title')"
    :error="error"
    :submit-action="del"
    :submit-enabled="submitEnabled && !$slots.error"
    :submit-label="$tc('global.button.delete')"
    submit-color="error"
    :submit-icon="icon"
    cancel-icon=""
    :cancel-action="close"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <slot>{{
      $tc('global.warning.delete', warningTextEntity ? 2 : 0, {
        entity: warningTextEntity,
      })
    }}</slot>
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
    warningTextEntity: { type: String, required: false, default: '' },
    dialogTitle: { type: String, required: false, default: '' },
    icon: { type: String, required: false, default: 'mdi-delete' },
  },
  created() {
    this.entityUri = this.entity._meta.self
  },
}
</script>

<style scoped></style>
