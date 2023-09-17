<template>
  <dialog-form
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-package-variant"
    :title="$tc('components.material.dialogMaterialItemEdit.title')"
    :submit-action="update"
    submit-color="success"
    :cancel-action="close"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>

    <template #moreActions>
      <ButtonDelete class="v-btn--has-bg" @click="del" />
    </template>

    <dialog-material-item-form
      :material-lists="camp.materialLists"
      :material-item="entityData"
    />
  </dialog-form>
</template>

<script>
import DialogBase from '@/components/dialog/DialogBase.vue'
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogMaterialItemForm from './DialogMaterialItemForm.vue'
import ButtonDelete from '@/components/buttons/ButtonDelete.vue'

export default {
  name: 'DialogMaterialItemEdit',
  components: { ButtonDelete, DialogForm, DialogMaterialItemForm },
  extends: DialogBase,
  props: {
    materialItemUri: { type: String, required: true },
  },
  data() {
    return {
      entityProperties: ['quantity', 'unit', 'article'],
      embeddedEntities: ['materialList'],
    }
  },
  computed: {
    materialItem() {
      return this.api.get(this.materialItemUri)
    },
    camp() {
      return this.materialItem.materialList().camp()
    },
  },
  watch: {
    // copy data whenever dialog is opened
    showDialog: function (showDialog) {
      if (showDialog) {
        this.loadEntityData(this.materialItemUri)
      }
    },
  },
}
</script>

<style scoped></style>
