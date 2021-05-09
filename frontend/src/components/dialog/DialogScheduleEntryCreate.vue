<template>
  <dialog-form
    v-model="showDialog"
    icon="mdi-calendar-plus"
    :title="$tc('components.dialog.dialogScheduleEntryCreate.title')"
    max-width="600px"
    :submit-action="createScheduleEntry"
    submit-color="success"
    :cancel-action="close">
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <server-error :server-error="error" />
    <dialog-schedule-entry-form
      :schedule-entry="entityData"
      :camp="camp" />
  </dialog-form>
</template>

<script>
import DialogBase from './DialogBase.vue'
import DialogForm from './DialogForm.vue'
import DialogScheduleEntryForm from './DialogScheduleEntryForm.vue'
import ServerError from '@/components/form/ServerError.vue'

export default {
  name: 'DialogScheduleEntryCreate',
  components: { DialogForm, DialogScheduleEntryForm, ServerError },
  extends: DialogBase,
  props: {
    camp: { type: Function, required: true },
    activity: { type: Function, required: true },
    scheduleEntry: { type: Object, required: false, default: null }
  },
  data () {
    return {
      entityProperties: [
        'periodId',
        'activityId',
        'periodOffset',
        'length'
      ],
      entityUri: '/schedule-entries'
    }
  },
  watch: {
    // copy data whenever dialog is opened
    showDialog: function (showDialog) {
      if (showDialog) {
        if (this.scheduleEntry != null) {
          this.setEntityData({
            activityId: this.activity().id,
            periodId: this.scheduleEntry.period().id,
            periodOffset: this.scheduleEntry.periodOffset,
            length: this.scheduleEntry.length
          })
        } else {
          this.setEntityData({
            activityId: this.activity().id,
            periodId: null,
            periodOffset: 0,
            length: 0
          })
        }
      } else {
        this.clearEntityData()
      }
    }
  },
  methods: {
    createScheduleEntry () {
      return this.create().then(() => {
        this.api.reload(this.activity())
      })
    }
  }
}
</script>

<style scoped>

</style>
