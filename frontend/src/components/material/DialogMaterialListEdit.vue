<template>
  <DetailEdit
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-package-variant"
    :title="$tc('components.material.dialogMaterialListEdit.title')"
    :submit-action="update"
    submit-color="success"
    :cancel-action="close"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>

    <template #moreActions>
      <PromptEntityDelete :entity="materialList">
        <template #activator="{ on }">
          <button-delete v-on="on" />
        </template>
        <i18n path="components.material.dialogMaterialListDelete.description">
          <template #entity
            ><strong>{{ materialList.name }}</strong></template
          >
        </i18n>
      </PromptEntityDelete>
    </template>
    <dialog-material-list-form :material-list="entityData" />
  </DetailEdit>
</template>

<script>
import DialogBase from '@/components/dialog/DialogBase.vue'
import DialogMaterialListForm from './DialogMaterialListForm.vue'
import DetailEdit from '@/components/generic/DetailPane.vue'
import ButtonDelete from '@/components/buttons/ButtonDelete.vue'
import PromptEntityDelete from '@/components/prompt/PromptEntityDelete.vue'

export default {
  name: 'DialogMaterialListEdit',
  components: {
    PromptEntityDelete,
    ButtonDelete,
    DetailEdit,
    DialogMaterialListForm,
  },
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
}
</script>

<style scoped></style>
