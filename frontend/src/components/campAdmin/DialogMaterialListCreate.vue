<template>
  <dialog-form
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-package-variant"
    :title="$tc('components.campAdmin.dialogMaterialListCreate.title')"
    :submit-action="createMaterialList"
    submit-color="success"
    :cancel-action="close"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>

    <dialog-material-list-form :material-list="entityData" />
  </dialog-form>
</template>

<script>
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogBase from '@/components/dialog/DialogBase.vue'
import DialogMaterialListForm from './DialogMaterialListForm.vue'

export default {
  name: 'DialogMaterialListCreate',
  components: { DialogForm, DialogMaterialListForm },
  extends: DialogBase,
  props: {
    camp: { type: Object, required: true },
  },
  data() {
    return {
      entityProperties: ['camp', 'name'],
      entityUri: '/material_lists',
    }
  },
  watch: {
    showDialog: function (showDialog) {
      if (showDialog) {
        this.setEntityData({
          camp: this.camp._meta.self,
          name: '',
        })
      } else {
        // clear form on exit
        this.clearEntityData()
      }
    },
  },
  methods: {
    createMaterialList() {
      return this.create().then(() => {
        this.api.reload(this.camp.materialLists())
      })
    },
  },
}
</script>

<style scoped></style>
