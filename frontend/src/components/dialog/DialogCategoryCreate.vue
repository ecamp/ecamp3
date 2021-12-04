<template>
  <dialog-form
    v-model="showDialog"
    :loading="loading"
    icon="mdi-calendar-plus"
    :title="$tc('components.dialog.dialogCategoryCreate.title')"
    max-width="600px"
    :submit-action="createCategory"
    submit-color="success"
    :cancel-action="close">
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>

    <dialog-category-form
      :camp="camp"
      :is-new="true"
      :category="entityData" />
  </dialog-form>
</template>

<script>
import DialogForm from './DialogForm.vue'
import DialogBase from './DialogBase.vue'
import DialogCategoryForm from './DialogCategoryForm.vue'

export default {
  name: 'DialogCategoryCreate',
  components: {
    DialogCategoryForm,
    DialogForm
  },
  extends: DialogBase,
  props: {
    camp: { type: Object, required: true }
  },
  data () {
    return {
      entityProperties: [
        'camp',
        'short',
        'name',
        'color',
        'numberingStyle'
      ],
      embeddedCollections: [
        'preferredContentTypes'
      ],
      entityUri: '/categories'
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
          numberingStyle: '1'
        })
      } else {
        // clear form on exit
        this.clearEntityData()
      }
    }
  },
  methods: {
    createCategory () {
      return this.create().then(() => {
        this.api.reload(this.camp.categories())
      })
    }
  }
}
</script>

<style scoped>

</style>
