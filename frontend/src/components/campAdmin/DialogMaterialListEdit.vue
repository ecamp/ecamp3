<template>
  <dialog-form
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-package-variant"
    :title="'Materialliste: ' + materialList.name"
    max-width="600px"
    :submit-action="update"
    submit-color="success"
    :cancel-action="close">
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <template #moreActions>
      <dialog-entity-delete :entity="materialList">
        <template #activator="{ on }">
          <button-delete v-on="on" />
        </template>
        {{ $tc('components.camp.campMaterialListsItem.deleteWarning') }} <br>
        <ul>
          <li>
            {{ materialList.name }}
          </li>
        </ul>
      </dialog-entity-delete>
    </template>
    <dialog-material-list-form :material-list="entityData" />
  </dialog-form>
</template>

<script>
import DialogBase from '@/components/dialog/DialogBase.vue'
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogMaterialListForm from './DialogMaterialListForm.vue'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete.vue'
import ButtonDelete from '@/components/buttons/ButtonDelete.vue'

export default {
  name: 'DialogMaterialListEdit',
  components: { ButtonDelete, DialogEntityDelete, DialogForm, DialogMaterialListForm },
  extends: DialogBase,
  props: {
    materialList: { type: Object, required: true }
  },
  data () {
    return {
      entityProperties: [
        'name'
      ]
    }
  },
  watch: {
    // copy data whenever dialog is opened
    showDialog: function (showDialog) {
      if (showDialog) {
        this.loadEntityData(this.materialList._meta.self)
      }
    }
  }
}
</script>

<style scoped>

</style>
