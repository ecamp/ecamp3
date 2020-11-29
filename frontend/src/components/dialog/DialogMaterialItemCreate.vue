<template>
  <dialog-form
    v-model="showDialog"
    icon="mdi-package-variant"
    :title="this.$tc('components.dialog.dialogMaterialListCreate.title')"
    max-width="600px"
    :submit-action="createMaterialItem"
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
import DialogForm from './DialogForm'
import DialogBase from './DialogBase'
import DialogMaterialItemForm from './DialogMaterialItemForm'
import ServerError from '@/components/form/ServerError'

export default {
  name: 'DialogMaterialItemCreate',
  components: { DialogForm, DialogMaterialItemForm, ServerError },
  extends: DialogBase,
  props: {
    camp: { type: Object, required: true },
    period: { type: Object, required: false, default: null }
  },
  data () {
    return {
      entityProperties: [
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
        this.setEntityData({
          periodId: this.period.id,
          quantity: '',
          unit: '',
          article: '',
          materialListId: null
        })
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
