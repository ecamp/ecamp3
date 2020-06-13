<template>
  <dialog-form
    v-model="showDialog"
    icon="mdi-calendar-plus"
    title="Create ActivityCategory"
    max-width="600px"
    :submit-action="createPeriod"
    submit-color="success"
    :cancel-action="close">
    <template v-slot:activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>

    <dialog-activity-category-form v-if="!loading" :activity-category="entityData" />
  </dialog-form>
</template>

<script>
import DialogForm from './DialogForm'
import DialogBase from './DialogBase'
import DialogActivityCategoryForm from './DialogActivityCategoryForm'

export default {
  name: 'DialogActivityCategoryCreate',
  components: {
    DialogActivityCategoryForm,
    DialogForm
  },
  extends: DialogBase,
  props: {
    camp: { type: Object, required: true }
  },
  data () {
    return {
      entityProperties: [
        'campId',
        'short',
        'name',
        'activityType',
        'color',
        'numberingStyle'
      ],
      entityUri: '/activity-categories'
    }
  },
  watch: {
    showDialog: function (showDialog) {
      if (showDialog) {
        this.setEntityData({
          campId: this.camp.id,
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
    createPeriod () {
      return this.create().then(() => {
        this.api.reload(this.camp)
      })
    }
  }
}
</script>

<style scoped>

</style>
