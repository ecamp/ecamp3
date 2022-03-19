<template>
  <dialog-form
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-package-variant"
    :title="materialList.name"
    max-width="600px"
    :submit-action="update"
    submit-color="success"
    :cancel-action="close">
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <dialog-material-list-form :material-list="entityData" />
  </dialog-form>
</template>

<script>
import DialogBase from '@/components/dialog/DialogBase.vue'
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogMaterialListForm from './DialogMaterialListForm.vue'

export default {
  name: 'DialogMaterialListEdit',
  components: { DialogForm, DialogMaterialListForm },
  extends: DialogBase,
  props: {
    materialList: { type: Object, required: true }
  },
  data () {
    return {
      entityProperties: [
        'name'
      ]
    }
  },
  watch: {
    // copy data whenever dialog is opened
    showDialog: function (showDialog) {
      if (showDialog) {
        this.loadEntityData(this.materialList._meta.self)
      }
    }
  }
}
</script>

<style scoped>

</style>
