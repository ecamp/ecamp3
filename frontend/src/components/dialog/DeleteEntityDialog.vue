<template>
  <dialog-form
    v-model="visible"
    icon="mdi-delete"
    title="Delete"
    max-width="600px"
    :del="del"
    :cancel="cancel">
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
  watch: {
    value: function (uri) {
      this.loadEntityData(uri)
    }
  }
}
</script>

<style scoped>

</style>
