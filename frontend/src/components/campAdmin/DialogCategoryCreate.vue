<template>
  <dialog-form
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-calendar-plus"
    :title="$tc('components.campAdmin.dialogCategoryCreate.title')"
    :submit-action="createCategory"
    :submit-label="$tc('global.button.create')"
    submit-color="success"
    :cancel-action="close"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>

    <dialog-category-form :camp="camp" :is-new="true" :category="entityData" />
  </dialog-form>
</template>

<script>
import { categoryRoute } from '@/router.js'
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogBase from '@/components/dialog/DialogBase.vue'
import DialogCategoryForm from './DialogCategoryForm.vue'

export default {
  name: 'DialogCategoryCreate',
  components: {
    DialogCategoryForm,
    DialogForm,
  },
  extends: DialogBase,
  props: {
    camp: { type: Object, required: true },
  },
  data() {
    return {
      entityProperties: ['camp', 'short', 'name', 'color', 'numberingStyle'],
      embeddedCollections: ['preferredContentTypes'],
      entityUri: '/categories',
    }
  },
  watch: {
    showDialog: function (showDialog) {
      if (showDialog) {
        this.setEntityData({
          camp: this.camp._meta.self,
          short: '',
          name: '',
          color: '#000000',
          numberingStyle: '1',
        })
      } else {
        // clear form on exit
        this.clearEntityData()
      }
    },
  },
  methods: {
    async createCategory() {
      const createdCategory = await this.create()
      await this.api.reload(this.camp.categories())
      this.$router.push(categoryRoute(this.camp, createdCategory))
    },
  },
}
</script>

<style scoped></style>
