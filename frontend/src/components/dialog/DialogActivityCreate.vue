<template>
  <dialog-form
    v-model="showDialog"
    icon="mdi-calendar-plus"
    :title="$tc('entity.activity.new')"
    max-width="600px"
    :submit-action="createActivity"
    submit-color="success"
    :cancel-action="close">
    <template v-slot:activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>

    <dialog-activity-form v-if="!loading" :activity="entityData" />
  </dialog-form>
</template>

<script>
import DialogForm from './DialogForm'
import DialogBase from './DialogBase'
import DialogActivityForm from './DialogActivityForm'

export default {
  name: 'DialogActivityCreate',
  components: {
    DialogForm,
    DialogActivityForm
  },
  extends: DialogBase,
  props: {
    scheduleEntry: {
      type: Object,
      required: true
    }
  },
  data () {
    return {
      entityProperties: [
        'title',
        'activityCategoryId',
        'scheduleEntries',
        'location',
        'camp'
      ],
      entityUri: '/activities'
    }
  },
  watch: {
    showDialog: function (showDialog) {
      if (showDialog) {
        const newScheduleEntry = {
          periodOffset: this.scheduleEntry.periodOffset,
          length: this.scheduleEntry.length,
          period: this.scheduleEntry.period
        }
        this.setEntityData({
          camp: this.scheduleEntry.activity().camp,
          title: this.$tc('entity.activity.new'),
          location: '',
          scheduleEntries: [
            newScheduleEntry
          ]
        })
      } else {
        // clear form on exit
        this.clearEntityData()
      }
    }
  },
  methods: {
    createActivity () {
      return this.create().then(() => {
        this.api.reload(this.scheduleEntry.period.scheduleEntries()).then(
          this.$emit('activityCreated')
        )
      })
    }
  }
}
</script>

<style scoped>

</style>
