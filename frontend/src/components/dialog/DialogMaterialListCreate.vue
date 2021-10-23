<template>
  <dialog-form
    v-model="showDialog"
    icon="mdi-package-variant"
    :title="$tc('components.dialog.dialogMaterialListCreate.title')"
    max-width="600px"
    :submit-action="createMaterialList"
    submit-color="success"
    :cancel-action="close">
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <server-error :server-error="error" />
    <dialog-material-list-form :material-list="entityData" />
  </dialog-form>
</template>

<script>
import DialogForm from './DialogForm.vue'
import DialogBase from './DialogBase.vue'
import DialogMaterialListForm from './DialogMaterialListForm.vue'
import ServerError from '@/components/form/ServerError.vue'

export default {
  name: 'DialogMaterialListCreate',
  components: { DialogForm, DialogMaterialListForm, ServerError },
  extends: DialogBase,
  props: {
    camp: { type: Object, required: true }
  },
  data () {
    return {
      entityProperties: [
        'campId',
        'name'
      ],
      entityUri: this.api.get().materialList
    }
  },
  watch: {
    showDialog: function (showDialog) {
      if (showDialog) {
        this.setEntityData({
          campId: this.camp.id,
          name: ''
        })
      } else {
        // clear form on exit
        this.clearEntityData()
      }
    }
  },
  methods: {
    createMaterialList () {
      return this.create().then(() => {
        this.api.reload(this.camp.materialLists())
      })
    }
  }
}
</script>

<style scoped>

</style>
