<template>
  <dialog-form
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-package-variant"
    :title="$tc('components.material.dialogMaterialItemCreate.title')"
    :submit-action="createMaterialItem"
    submit-color="success"
    :cancel-action="close"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>

    <dialog-material-item-form
      :material-lists="camp.materialLists()"
      :material-item="entityData"
    />
  </dialog-form>
</template>

<script>
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogBase from '@/components/dialog/DialogBase.vue'
import DialogMaterialItemForm from './DialogMaterialItemForm.vue'

export default {
  name: 'DialogMaterialItemCreate',
  components: { DialogForm, DialogMaterialItemForm },
  extends: DialogBase,
  props: {
    camp: { type: Object, required: true },

    materialList: { type: Object, required: false, default: null },

    // API collection on which to post the new item
    materialItemCollection: { type: Object, required: true },
  },
  data() {
    return {
      entityProperties: ['quantity', 'unit', 'article'],
      embeddedEntities: ['materialList'],
      entityUri: this.materialItemCollection._meta.self,
    }
  },
  watch: {
    showDialog: function (showDialog) {
      if (showDialog) {
        const entityData = {
          quantity: null,
          unit: '',
          article: '',
          materialList: this.materialList,
        }

        this.setEntityData(entityData)
      } else {
        // clear form on exit
        this.clearEntityData()
      }
    },
  },
  methods: {
    createMaterialItem() {
      const key = Date.now()
      const data = this.entityData

      // fire event to allow for eager adding before post has finished
      this.$emit('item-adding', key, data)
      this.close()
    },
  },
}
</script>

<style scoped></style>
