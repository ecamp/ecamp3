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
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>

    <dialog-activity-form v-if="!loading" :activity="entityData" :camp="camp" />
  </dialog-form>
</template>

<script>
import DialogForm from './DialogForm.vue'
import DialogBase from './DialogBase.vue'
import DialogActivityForm from './DialogActivityForm.vue'

export default {
  name: 'DialogActivityCreate',
  components: {
    DialogForm,
    DialogActivityForm
  },
  extends: DialogBase,
  props: {
    camp: { type: Function, required: true },
    scheduleEntry: { type: Object, required: true }
  },
  data () {
    return {
      entityProperties: [
        'title',
        'location',
        'scheduleEntries'
      ],
      embeddedEntities: [
        'category'
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
            {
              period: this.scheduleEntry.period,
              periodOffset: this.scheduleEntry.periodOffset,
              length: this.scheduleEntry.length
            }
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
      const entityData = {
        ...this.entityData,
        scheduleEntries: this.entityData.scheduleEntries?.map(entry => ({
          period: entry.period()._meta.self,
          periodOffset: entry.periodOffset,
          length: entry.length
        })) || []
      }
      return this.api.post(this.entityUri, entityData).then(this.createSuccessful, this.onError)
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
