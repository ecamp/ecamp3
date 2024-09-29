<template>
  <dialog-form
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-package-variant"
    :title="$tc('components.campAdmin.dialogMaterialListEdit.title')"
    :submit-action="update"
    submit-color="success"
    :cancel-action="close"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <template #moreActions>
      <PromptEntityDelete
        :entity="entityUri"
        :warning-text-entity="materialList.name"
        :error-handler="deleteErrorHandler"
        position="top"
        align="left"
      />
    </template>
    <dialog-material-list-form :material-list="entityData" />
  </dialog-form>
</template>

<script>
import DialogBase from '@/components/dialog/DialogBase.vue'
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogMaterialListForm from './DialogMaterialListForm.vue'
import PromptEntityDelete from '@/components/prompt/PromptEntityDelete.vue'

export default {
  name: 'DialogMaterialListEdit',
  components: { PromptEntityDelete, DialogForm, DialogMaterialListForm },
  extends: DialogBase,
  props: {
    materialList: { type: Object, required: true },
  },
  data() {
    return {
      entityProperties: ['name'],
    }
  },
  watch: {
    // copy data whenever dialog is opened
    showDialog: function (showDialog) {
      if (showDialog) {
        this.loadEntityData(this.materialList._meta.self)
      }
    },
  },
  methods: {
    deleteErrorHandler(e) {
      if (e?.response?.status === 422 /* Validation Error */) {
        return this.$tc('components.campAdmin.dialogMaterialListEdit.deleteError')
      }
      return null
    },
  },
}
</script>

<style scoped></style>
