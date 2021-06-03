<template>
  <dialog-form
    v-model="showDialog"
    icon="mdi-calendar-edit"
    :title="scheduleEntry.activity().title"
    max-width="600px"
    :submit-action="update"
    submit-color="success"
    :cancel-action="close">
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <server-error :server-error="error" />
    <dialog-schedule-entry-form
      :schedule-entry="entityData"
      :camp="scheduleEntry.period().camp" />
  </dialog-form>
</template>

<script>
import DialogBase from './DialogBase.vue'
import DialogForm from './DialogForm.vue'
import DialogScheduleEntryForm from './DialogScheduleEntryForm.vue'
import ServerError from '@/components/form/ServerError.vue'

export default {
  name: 'DialogScheduleEntryEdit',
  components: { DialogForm, DialogScheduleEntryForm, ServerError },
  extends: DialogBase,
  props: {
    scheduleEntry: { type: Object, required: true }
  },
  data () {
    return {
      entityProperties: [
        'periodOffset',
        'length'
      ],
      embeddedEntities: [
        'period'
      ]
    }
  },
  watch: {
    // copy data whenever dialog is opened
    showDialog: function (showDialog) {
      if (showDialog) {
        this.loadEntityData(this.scheduleEntry._meta.self)
      }
    }
  }
}
</script>

<style scoped>

</style>
