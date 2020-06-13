<template>
  <dialog-form
    v-model="showDialog"
    icon="mdi-delete"
    title="Delete"
    max-width="600px"
    :submit-action="del"
    :submit-enabled="!$slots.error"
    submit-label="button.delete"
    submit-color="error"
    submit-icon="mdi-delete"
    :cancel-action="close">
    <template v-slot:activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <slot />
    <template v-slot:error v-if="$slots.error">
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
