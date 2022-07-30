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
    :cancel-action="close"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <template #moreActions>
      <v-btn
        v-if="!scheduleEntry.tmpEvent"
        color="primary"
        :to="scheduleEntryRoute(scheduleEntry)"
      >
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
    scheduleEntry: { type: Object, required: true },
  },
  data() {
    return {
      entityProperties: ['title', 'location'],
      embeddedEntities: ['category'],
    }
  },
  computed: {
    scheduleEntries() {
      return this.activity.scheduleEntries()
    },
    activity() {
      return this.scheduleEntry.activity()
    },
  },
  watch: {
    showDialog: async function (showDialog) {
      if (showDialog) {
        this.loadEntityData(this.activity._meta.self)

        const scheduleEntries = await this.scheduleEntries.$loadItems()
        this.$set(
          this.entityData,
          'scheduleEntries',
          scheduleEntries.items.map((scheduleEntry) => {
            return {
              period: scheduleEntry.period,
              start: scheduleEntry.start,
              end: scheduleEntry.end,
              key: scheduleEntry._meta.self,
              deleted: false,
              self: scheduleEntry._meta.self,
            }
          })
        )
      }
    },
  },
  methods: {
    updateActivity() {
      this.error = null
      const _events = this._events

      const promises = this.entityData.scheduleEntries.map((entry) => {
        // deleted local entry: do nothing
        if (!entry.self && entry.deleted) {
          return Promise.resolve()
        }

        // delete existing
        if (entry.self && entry.deleted) {
          return this.api.del(entry.self)
        }

        // update existing
        if (entry.self) {
          return this.api.patch(entry.self, {
            period: entry.period()._meta.self,
            start: entry.start,
            end: entry.end,
          })
        }

        // else: create new entry
        return this.scheduleEntries.$post({
          period: entry.period()._meta.self,
          start: entry.start,
          end: entry.end,
          activity: this.activity._meta.self,
        })
      })

      // patch activity entity
      const activityPayload = { ...this.entityData }
      delete activityPayload.scheduleEntries
      promises.push(this.api.patch(this.entityUri, activityPayload))

      // execute all requests together --> onError if one fails
      const promise = Promise.all(promises).then(this.updatedSuccessful, (e) => {
        this.onError(_events, e)
      })

      this.$emit('submit')
      return promise
    },
    updatedSuccessful(data) {
      this.close()
      this.$emit('activityUpdated', data)
    },
    scheduleEntryRoute,
  },
}
</script>

<style scoped></style>
