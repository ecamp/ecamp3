<template>
  <dialog-form
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-calendar-plus"
    max-width="600px"
    :submit-action="updateActivity"
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
    <dialog-activity-form :activity="entityData" :period="scheduleEntry.period" />
  </dialog-form>
</template>

<script>
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogBase from '@/components/dialog/DialogBase.vue'
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
      ]
    }
  },
  computed: {
    scheduleEntries () {
      return this.activity.scheduleEntries()
    },
    activity () {
      return this.scheduleEntry.activity()
    }
  },
  watch: {
    showDialog: async function (showDialog) {
      if (showDialog) {
        this.loadEntityData(this.activity._meta.self)

        const scheduleEntries = await this.scheduleEntries.$loadItems()
        this.$set(this.entityData, 'scheduleEntries', scheduleEntries.items.map((scheduleEntry) => {
          return {
            period: scheduleEntry.period,
            periodOffset: scheduleEntry.periodOffset,
            length: scheduleEntry.length,
            key: scheduleEntry._meta.self,
            '@id': scheduleEntry._meta.self
          }
        }))
      }
    }
  },
  methods: {
    /**
     * Following code only works, if embedded collection patch is enabled
     */
    /*
    updateActivity () {
      const payloadData = {
        ...this.entityData,

        scheduleEntries: this.entityData.scheduleEntries?.map(entry => ({
          period: entry.period()._meta.self,
          periodOffset: entry.periodOffset,
          length: entry.length,
          id: entry['@id']
        })) || []
      }

      return this.update(payloadData)
    }, */

    updateActivity () {
      this.error = null
      const _events = this._events

      const scheduleEntryPromises = this.entityData.scheduleEntries
        .map(entry => {
          if (entry['@id']) {
            return this.api.patch(entry['@id'], {
              period: entry.period()._meta.self,
              periodOffset: entry.periodOffset,
              length: entry.length
            })
          } else {
            return this.scheduleEntries.$post({
              period: entry.period()._meta.self,
              periodOffset: entry.periodOffset,
              length: entry.length,
              activity: this.activity._meta.self
            })
          }
        })

      // patch activity entity
      const activityPayload = { ...this.entityData }
      delete activityPayload.scheduleEntries

      // first patch + post + delete all schedule entries
      const promise = Promise.all(scheduleEntryPromises)

        // then patch the activity itself (ensures after return we have a valid & complete activity in the local store)
        .then(() => this.api.patch(this.entityUri, activityPayload))
        .then(this.updatedSuccessful, e => this.onError(_events, e))

      this.$emit('submit')
      return promise
    },
    updatedSuccessful (data) {
      this.close()
      this.$emit('activityUpdated', data)
    },
    scheduleEntryRoute
  }
}
</script>

<style scoped>

</style>
