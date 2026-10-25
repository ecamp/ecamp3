<template>
  <dialog-form
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-package-variant"
    :title="$t('components.material.dialogMaterialItemEdit.title')"
    :submit-action="updateMaterialItem"
    :submit-enabled="!creatingMaterialList"
    submit-color="success"
    :cancel-action="close"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>

    <template #moreActions>
      <PromptEntityDelete
        :entity="materialItemUri"
        :success-handler="deleteSuccessHandler"
        :warning-text-entity="materialItem.article"
        align="left"
        position="top"
        :btn-attrs="{
          class: 'v-btn--has-bg',
        }"
      />
    </template>

    <dialog-material-item-form
      :camp="camp"
      :material-item="entityData"
      @update:creating="creatingMaterialList = $event"
    />
  </dialog-form>
</template>

<script>
import DialogBase from '@/components/dialog/DialogBase.vue'
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogMaterialItemForm from './DialogMaterialItemForm.vue'
import PromptEntityDelete from '@/components/prompt/PromptEntityDelete.vue'

export default {
  name: 'DialogMaterialItemEdit',
  components: { PromptEntityDelete, DialogForm, DialogMaterialItemForm },
  extends: DialogBase,
  props: {
    materialItemUri: { type: String, required: true },
    deleteSuccessHandler: { type: Function, default: null },
  },
  data() {
    return {
      entityProperties: ['quantity', 'unit', 'article'],
      embeddedEntities: ['materialList'],
      creatingMaterialList: false,
    }
  },
  computed: {
    materialItem() {
      return this.api.get(this.materialItemUri)
    },
    camp() {
      return this.materialItem.camp()
    },
  },
  watch: {
    // copy data whenever dialog is opened
    showDialog: function (showDialog) {
      if (showDialog) {
        this.creatingMaterialList = false
        this.loadEntityData(this.materialItemUri)
      }
    },
  },
  methods: {
    updateMaterialItem() {
      if (this.creatingMaterialList) return

      return this.update()
    },
  },
}
</script>

<style scoped></style>
