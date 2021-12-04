<template>
  <dialog-form
    v-model="showDialog"
    :loading="loading"
    icon="mdi-calendar-plus"
    max-width="600px"
    :submit-action="update"
    submit-label="global.button.update"
    submit-color="success"
    :cancel-action="close">
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <template #moreActions>
      <v-btn v-if="!scheduleEntry.tmpEvent"
             color="primary" :to="scheduleEntryRoute(scheduleEntry)">
        {{ $tc('global.button.open') }}
      </v-btn>
    </template>
    <dialog-activity-form :activity="entityData" :camp="scheduleEntry.period().camp" />
  </dialog-form>
</template>

<script>
import DialogForm from './DialogForm.vue'
import DialogBase from './DialogBase.vue'
import DialogActivityForm from './DialogActivityForm.vue'
import { scheduleEntryRoute } from '@/router.js'

export default {
  name: 'DialogActivityEdit',
  components: { DialogForm, DialogActivityForm },
  extends: DialogBase,
  props: {
    scheduleEntry: { type: Object, required: true }
  },
  data () {
    return {
      entityProperties: [
        'title',
        'location'
      ],
      embeddedEntities: [
        'category'
      ],
      embeddedCollections: [
        'scheduleEntries'
      ]
    }
  },
  watch: {
    showDialog: function (showDialog) {
      if (showDialog) {
        this.loadEntityData(this.scheduleEntry.activity()._meta.self)
      }
    }
  },
  methods: {
    updateActivity () {
      return this.update()
    },
    update () {
      this.error = null
      const patchScheduleEntries = this.entityData.scheduleEntries
        .map(entry => this.api.patch(entry._meta.self, {
          periodOffset: entry.periodOffset,
          length: entry.length
        }))
      Promise.all(patchScheduleEntries)
        .then(__ => ({ ...this.entityData }))
        .then(entityData => {
          delete entityData.scheduleEntries
          return entityData
        })
        .then(entityData => this.api.patch(this.entityUri, entityData))
        .then(this.updatedSuccessful, this.onError)
    },
    updatedSuccessful (data) {
      this.api.reload(this.scheduleEntry).then(() => {
        this.close()
        this.$emit('scheduleEntryUpdated', data)
      })
    },
    scheduleEntryRoute
  }
}
</script>

<style scoped>

</style>
