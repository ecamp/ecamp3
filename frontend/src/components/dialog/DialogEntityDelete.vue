<template>
  <dialog-form
    v-model="showDialog"
    icon="mdi-delete"
    :title="$t('components.dialog.dialogEntityDelete.title')"
    max-width="600px"
    :submit-action="del"
    :submit-enabled="!$slots.error"
    submit-label="global.button.delete"
    submit-color="error"
    submit-icon="mdi-delete"
    :cancel-action="close">
    <template v-slot:activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <slot>{{ $t('components.dialog.dialogEntityDelete.warningText') }}</slot>
    <template v-if="$slots.error || error" v-slot:error>
      <slot name="error">
        {{ error }}
      </slot>
    </template>
  </dialog-form>
</template>

<script>
import DialogForm from './DialogForm'
import DialogBase from './DialogBase'

export default {
  name: 'DialogEntityDelete',
  components: { DialogForm },
  extends: DialogBase,
  props: {
    entity: { type: Object, required: true }
  },
  created () {
    this.entityUri = this.entity._meta.self
  }
}
</script>

<style scoped>

</style>
