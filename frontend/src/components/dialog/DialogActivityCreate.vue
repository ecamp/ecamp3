<template>
  <dialog-form
    v-model="showDialog"
    icon="mdi-calendar-plus"
    :title="$tc('entity.activity.new')"
    max-width="600px"
    :submit-action="createActivity"
    submit-label="global.button.create"
    submit-icon="mdi-plus"
    submit-color="success"
    :cancel-action="cancelCreate">
    <template v-slot:activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>

    <dialog-activity-form v-if="!loading" :activity="entityData" :camp="scheduleEntry.period().camp" />
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
        'location'
      ],
      entityUri: '/activities'
    }
  },
  watch: {
    showDialog: function (showDialog) {
      if (showDialog) {
        this.setEntityData({
          title: this.$tc('entity.activity.new'),
          location: '',
          scheduleEntries: [
            this.scheduleEntry
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
      return this.create()
    },
    cancelCreate () {
      this.close()
      this.$emit('creationCanceled')
    },
    create () {
      this.error = null
      return this.api.post(this.entityUri, this.entityData).then(this.createSuccessful, this.onError)
    },
    createSuccessful (data) {
      data.scheduleEntries()._meta.load.then(() => {
        this.close()
        this.$emit('activityCreated', data)
      })
    }
  }
}
</script>

<style scoped>

</style>
