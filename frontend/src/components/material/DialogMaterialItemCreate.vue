<template>
  <dialog-form
    v-model="showDialog"
    :loading="loading"
    icon="mdi-package-variant"
    :title="$tc('components.dialog.dialogMaterialItemCreate.title')"
    max-width="600px"
    :submit-action="createMaterialItem"
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
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogBase from '@/components/dialog/DialogBase.vue'
import DialogMaterialItemForm from './DialogMaterialItemForm.vue'
import ServerError from '@/components/form/ServerError.vue'

export default {
  name: 'DialogMaterialItemCreate',
  components: { DialogForm, DialogMaterialItemForm, ServerError },
  extends: DialogBase,
  props: {
    camp: { type: Object, required: true },

    // API collection on which to post the new item
    materialItemCollection: { type: Object, required: true }
  },
  data () {
    return {
      entityProperties: [
        'quantity',
        'unit',
        'article'
      ],
      entityUri: this.materialItemCollection._meta.self
    }
  },
  watch: {
    showDialog: function (showDialog) {
      if (showDialog) {
        const entityData = {
          quantity: '',
          unit: '',
          article: '',
          materialList: null
        }

        this.setEntityData(entityData)
      } else {
        // clear form on exit
        this.clearEntityData()
      }
    }
  },
  methods: {
    createMaterialItem () {
      const key = Date.now()
      const data = this.entityData

      // fire event to allow for eager adding before post has finished
      this.$emit('item-adding', key, data)
      this.close()
    }
  }
}
</script>

<style scoped>

</style>
