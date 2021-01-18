<template>
  <dialog-form
    v-model="showDialog"
    icon="mdi-calendar-edit"
    :title="activityCategory.name"
    max-width="600px"
    :submit-action="update"
    :cancel-action="close">
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>

    <dialog-activity-category-form
      v-if="!loading"
      :camp="camp"
      :is-new="false"
      :activity-category="entityData" />
  </dialog-form>
</template>

<script>
import DialogBase from '@/components/dialog/DialogBase'
import DialogForm from '@/components/dialog/DialogForm'
import DialogActivityCategoryForm from './DialogActivityCategoryForm'

export default {
  name: 'DialogActivityCategoryEdit',
  components: {
    DialogActivityCategoryForm,
    DialogForm
  },
  extends: DialogBase,
  props: {
    camp: { type: Object, required: true },
    activityCategory: { type: Object, required: true }
  },
  data () {
    return {
      entityProperties: [
        'short',
        'name',
        'color',
        'numberingStyle'
      ]
    }
  },
  watch: {
    // copy data whenever dialog is opened
    showDialog: function (showDialog) {
      if (showDialog) {
        this.loadEntityData(this.activityCategory._meta.self)
      }
    }
  }
}
</script>

<style scoped>

</style>
