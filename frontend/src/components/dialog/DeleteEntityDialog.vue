<template>
  <dialog-form
    v-model="showDialog"
    icon="mdi-delete"
    title="Delete"
    max-width="600px"
    :submit-action="del"
    submit-label="Delete"
    submit-color="error"
    submit-icon="mdi-delete"
    :cancel-action="close">
    <template v-slot:activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>

    <v-row>
      <v-col cols="12">
        Do you really want do delete <slot>this</slot>?
      </v-col>
    </v-row>
  </dialog-form>
</template>

<script>
import DialogForm from './DialogForm'
import DialogBase from './DialogBase'
export default {
  name: 'DeleteEntityDialog',
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
