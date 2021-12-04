<template>
  <dialog-form
    v-model="showDialog"
    :loading="loading"
    icon="mdi-package-variant"
    :title="materialList.name"
    max-width="600px"
    :submit-action="update"
    submit-color="success"
    :cancel-action="close">
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <server-error :server-error="error" />
    <dialog-material-list-form :material-list="entityData" />
  </dialog-form>
</template>

<script>
import DialogBase from './DialogBase.vue'
import DialogForm from './DialogForm.vue'
import DialogMaterialListForm from './DialogMaterialListForm.vue'
import ServerError from '@/components/form/ServerError.vue'

export default {
  name: 'DialogMaterialListEdit',
  components: { DialogForm, DialogMaterialListForm, ServerError },
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
