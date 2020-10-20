<template>
  <dialog-form
    v-model="showDialog"
    icon="mdi-calendar-plus"
    :title="this.$tc('components.dialog.dialogPeriodAdd.title')"
    max-width="600px"
    :submit-action="createPeriod"
    submit-color="success"
    :cancel-action="close">
    <template v-slot:activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>

    <dialog-period-form v-if="!loading" :period="entityData" />
  </dialog-form>
</template>

<script>
import DialogForm from './DialogForm'
import DialogBase from './DialogBase'
import DialogPeriodForm from './DialogPeriodForm'

export default {
  name: 'DialogPeriodCreate',
  components: { DialogForm, DialogPeriodForm },
  extends: DialogBase,
  props: {
    camp: { type: Object, required: true }
  },
  data () {
    return {
      entityProperties: [
        'campId',
        'description',
        'start',
        'end'
      ],
      entityUri: '/periods'
    }
  },
  watch: {
    showDialog: function (showDialog) {
      if (showDialog) {
        this.setEntityData({
          campId: this.camp.id,
          description: '',
          start: '',
          end: ''
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
