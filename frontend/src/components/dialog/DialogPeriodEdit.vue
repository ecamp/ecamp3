<template>
  <dialog-form
    v-model="showDialog"
    icon="mdi-calendar-edit"
    :title="period.description"
    max-width="600px"
    :submit-action="update"
    :cancel-action="close">
    <template v-slot:activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>

    <e-text-field
      v-model="entityData.description"
      label="Description"
      required />
    <e-text-field
      v-model="entityData.start"
      label="Start"
      required />
    <e-text-field
      v-model="entityData.end"
      label="End"
      required />
  </dialog-form>
</template>

<script>
import DialogBase from '@/components/dialog/DialogBase'
import DialogForm from '@/components/dialog/DialogForm'

export default {
  name: 'DialogPeriodEdit',
  components: { DialogForm },
  extends: DialogBase,
  props: {
    period: { type: Object, required: true }
  },
  data () {
    return {
      entityProperties: [
        'description',
        'start',
        'end'
      ]
    }
  },
  watch: {
    // copy data whenever dialog is opened
    showDialog: function (showDialog) {
      if (showDialog) {
        this.loadEntityData(this.period._meta.self)
      }
    }
  }
}
</script>

<style scoped>

</style>
