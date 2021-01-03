<template>
  <dialog-form
    v-model="showDialog"
    icon="mdi-calendar-edit"
    :title="period.description"
    max-width="600px"
    :submit-action="update"
    :cancel-action="close">
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <server-error :server-error="error" />
    <dialog-period-form v-if="!loading" :period="entityData" />
  </dialog-form>
</template>

<script>
import DialogBase from '@/components/dialog/DialogBase'
import DialogForm from '@/components/dialog/DialogForm'
import DialogPeriodForm from './DialogPeriodForm'
import ServerError from '@/components/form/ServerError'

export default {
  name: 'DialogPeriodEdit',
  components: { DialogForm, DialogPeriodForm, ServerError },
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
