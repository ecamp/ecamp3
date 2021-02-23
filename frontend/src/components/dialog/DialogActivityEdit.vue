<template>
  <dialog-form
    v-model="showDialog"
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
             color="primary" :to="scheduleEntryRoute(scheduleEntry.activity().camp(), scheduleEntry)">
        {{ $tc('global.button.open') }}
      </v-btn>
    </template>
    <dialog-activity-form v-if="!loading" :activity="entityData" :camp="scheduleEntry.period().camp" />
  </dialog-form>
</template>

<script>
import DialogForm from './DialogForm'
import DialogBase from './DialogBase'
import DialogActivityForm from './DialogActivityForm'
import { scheduleEntryRoute } from '@/router'

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
      return this.api.patch(this.entityUri, this.entityData).then(this.updatedSuccessful, this.onError)
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
