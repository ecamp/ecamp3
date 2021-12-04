<template>
  <dialog-form
    v-model="showDialog"
    :loading="loading"
    icon="mdi-package-variant"
    :title="title"
    max-width="600px"
    :submit-action="update"
    submit-color="success"
    :cancel-action="close">
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <server-error :server-error="error" />
    <dialog-material-item-form :material-lists="camp.materialLists" :material-item="entityData" />
  </dialog-form>
</template>

<script>
import DialogBase from '@/components/dialog/DialogBase.vue'
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogMaterialItemForm from './DialogMaterialItemForm.vue'
import ServerError from '@/components/form/ServerError.vue'

export default {
  name: 'DialogMaterialItemEdit',
  components: { DialogForm, DialogMaterialItemForm, ServerError },
  extends: DialogBase,
  props: {
    materialItemUri: { type: String, required: true }
  },
  data () {
    return {
      entityProperties: [
        'quantity',
        'unit',
        'article'
      ],
      embeddedEntities: [
        'materialList'
      ]
    }
  },
  computed: {
    materialItem () {
      return this.api.get(this.materialItemUri)
    },
    camp () {
      return this.materialItem.materialList().camp()
    },
    title () {
      return this.materialItem.quantity + ' ' + this.materialItem.unit + ' ' + this.materialItem.article
    }
  },
  watch: {
    // copy data whenever dialog is opened
    showDialog: function (showDialog) {
      if (showDialog) {
        this.loadEntityData(this.materialItemUri)
      }
    }
  }
}
</script>

<style scoped>

</style>
