<template>
  <dialog-form
    v-model="showDialog"
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
    <dialog-material-item-form :camp="camp" :material-item="entityData" />
  </dialog-form>
</template>

<script>
import DialogForm from './DialogForm.vue'
import DialogBase from './DialogBase.vue'
import DialogMaterialItemForm from './DialogMaterialItemForm.vue'
import ServerError from '@/components/form/ServerError.vue'

export default {
  name: 'DialogMaterialItemCreate',
  components: { DialogForm, DialogMaterialItemForm, ServerError },
  extends: DialogBase,
  props: {
    camp: { type: Object, required: true },
    period: { type: Object, required: false, default: null },
    contentNode: { type: Object, required: false, default: null }
  },
  data () {
    return {
      entityProperties: [
        'contentNodeId',
        'periodId',
        'quantity',
        'unit',
        'article'
      ],
      entityUri: '/material-items'
    }
  },
  watch: {
    showDialog: function (showDialog) {
      if (showDialog) {
        const entityData = {
          quantity: '',
          unit: '',
          article: '',
          materialListId: null
        }
        if (this.period != null) {
          entityData.periodId = this.period.id
        }
        if (this.contentNode != null) {
          entityData.contentNodeId = this.contentNode.id
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
      return this.create()
      // .then(mi => {
      //   this.api.reload(mi.materialList())
      // })
    }
  }
}
</script>

<style scoped>

</style>
