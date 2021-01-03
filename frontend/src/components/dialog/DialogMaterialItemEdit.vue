<template>
  <dialog-form
    v-model="showDialog"
    icon="mdi-package-variant"
    :title="title"
    max-width="600px"
    :submit-action="update"
    submit-color="success"
    :cancel-action="close">
    <template v-slot:activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <server-error :server-error="error" />
    <dialog-material-item-form :camp="camp" :material-item="entityData" />
  </dialog-form>
</template>

<script>
import DialogBase from './DialogBase'
import DialogForm from './DialogForm'
import DialogMaterialItemForm from './DialogMaterialItemForm'
import ServerError from '@/components/form/ServerError'

export default {
  name: 'DialogMaterialItemEdit',
  components: { DialogForm, DialogMaterialItemForm, ServerError },
  extends: DialogBase,
  props: {
    materialItem: { type: Object, required: true }
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
        this.loadEntityData(this.materialItem._meta.self)
      }
    }
  }
}
</script>

<style scoped>

</style>
