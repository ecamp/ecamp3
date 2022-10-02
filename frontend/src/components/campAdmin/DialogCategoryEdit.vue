<template>
  <dialog-form
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-calendar-edit"
    :title="category.name"
    :submit-action="update"
    :cancel-action="close"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>

    <dialog-category-form :camp="camp" :is-new="false" :category="entityData" />
  </dialog-form>
</template>

<script>
import DialogBase from '@/components/dialog/DialogBase.vue'
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogCategoryForm from './DialogCategoryForm.vue'

export default {
  name: 'DialogCategoryEdit',
  components: {
    DialogCategoryForm,
    DialogForm,
  },
  extends: DialogBase,
  props: {
    camp: { type: Object, required: true },
    category: { type: Object, required: true },
  },
  data() {
    return {
      entityProperties: ['short', 'name', 'color', 'numberingStyle'],
      embeddedCollections: ['preferredContentTypes'],
    }
  },
  watch: {
    // copy data whenever dialog is opened
    showDialog: function (showDialog) {
      if (showDialog) {
        this.loadEntityData(this.category._meta.self)
      }
    },
  },
}
</script>

<style scoped></style>
