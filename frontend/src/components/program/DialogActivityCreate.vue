<template>
  <dialog-form
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-calendar-plus"
    :title="$tc('entity.activity.new')"
    :submit-action="createActivity"
    :submit-label="$tc('global.button.create')"
    submit-icon="mdi-plus"
    submit-color="success"
    :cancel-action="cancelCreate"
    max-width="700px"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>

    <template #moreActions>
      <copy-activity-info-dialog @closed="refreshCopyActivitySource">
        <template #activator="{ on }">
          <v-btn v-show="clipboardPermission === 'prompt'" v-on="on">
            <v-icon left>mdi-information-outline</v-icon>
            {{ $tc('components.program.dialogActivityCreate.copyPastActivity') }}
          </v-btn>
        </template>
      </copy-activity-info-dialog>
    </template>

    <div v-if="clipboardUnsccessable">
      <e-text-field
        v-model="copyActivitySourceUrl"
        label="Aktivität kopieren"
        style="margin-bottom: 12px"
      >
        <template #append>
          <icon-with-tooltip
            tooltip-i18n-key="components.program.dialogActivityCreate.copySourceInfo"
            width="36"
            height="36"
          />
        </template>
      </e-text-field>
    </div>

    <div v-if="hasCopyActivitySource">
      <div class="mb-8">
        <div v-if="!clipboardUnsccessable">
          {{ $tc('components.program.dialogActivityCreate.clipboard') }}
          <div style="float: right">
            <small>
              <a
                href="#"
                style="color: inherit; text-decoration: none"
                @click="clearClipboard"
              >
                {{ $tc('components.program.dialogActivityCreate.clearClipboard') }}
              </a>
            </small>
          </div>
        </div>
        <v-list-item
          class="ec-copy-source rounded-xl blue-grey lighten-5 blue-grey--text text--darken-4 mt-1"
        >
          <v-list-item-avatar>
            <v-icon color="blue-grey">mdi-clipboard-check-outline</v-icon>
          </v-list-item-avatar>
          <v-list-item-content>
            <v-list-item-title>
              <CategoryChip
                :category="copyActivitySource.category()"
                class="mx-1"
                dense
              />
              {{ copyActivitySource.title }}
            </v-list-item-title>
            <v-list-item-subtitle>
              {{ copyActivitySource.camp().title }}
            </v-list-item-subtitle>
          </v-list-item-content>
          <v-list-item-action>
            <e-checkbox
              v-model="copyContent"
              :label="$tc('components.program.dialogActivityCreate.copyActivityContent')"
            />
          </v-list-item-action>
        </v-list-item>
      </div>
    </div>
    <dialog-activity-form :activity="entityData" :period="period" />
  </dialog-form>
</template>

<script>
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogBase from '@/components/dialog/DialogBase.vue'
import DialogActivityForm from './DialogActivityForm.vue'
import CopyActivityInfoDialog from '@/components/activity/CopyActivityInfoDialog.vue'
import { uniqueId } from 'lodash'
import router from '@/router.js'

export default {
  name: 'DialogActivityCreate',
  components: {
    DialogForm,
    DialogActivityForm,
    CopyActivityInfoDialog,
  },
  extends: DialogBase,
  props: {
    scheduleEntry: { type: Object, required: true },

    // currently visible period
    period: { type: Function, required: true },
  },
  data() {
    return {
      clipboardPermission: 'unknown',
      copyActivitySource: null,
      copyActivitySourceUrl: null,
      entityProperties: ['title', 'location', 'scheduleEntries'],
      embeddedEntities: ['category'],
      entityUri: '/activities',
    }
  },
  computed: {
    clipboardUnsccessable() {
      return this.clipboardPermission === 'unaccessable'
    },
    hasCopyActivitySource() {
      return this.copyActivitySource != null && this.copyActivitySource._meta.self != null
    },
    copyContent: {
      get() {
        return this.entityData.copyActivitySource != null
      },
      set(val) {
        if (val) {
          this.entityData.copyActivitySource = this.copyActivitySource._meta.self
          this.entityData.title = this.copyActivitySource.title
          if (
            this.period().camp()._meta.self == this.copyActivitySource.camp()._meta.self
          ) {
            this.entityData.category = this.copyActivitySource.category()._meta.self
          }
        } else {
          this.entityData.copyActivitySource = null
        }
      },
    },
  },
  watch: {
    showDialog: function (showDialog) {
      if (showDialog) {
        this.refreshCopyActivitySource()
        this.setEntityData({
          title: this.entityData?.title || this.$tc('entity.activity.new'),
          location: '',
          scheduleEntries: [
            {
              period: this.scheduleEntry.period,
              start: this.scheduleEntry.start,
              end: this.scheduleEntry.end,
              key: uniqueId(),
              deleted: false,
            },
          ],
          copyActivitySource: null,
        })
      } else {
        this.canReadClipboard = 'unknown'
        // clear the variable parts of the form on exit
        this.copyActivitySource = null
        this.copyActivitySourceUrl = null
        this.entityData.location = ''
        this.entityData.scheduleEntries = []
      }
    },
    copyActivitySourceUrl: function (url) {
      this.getCopyActivitySource(url).then((activity) => {
        this.$set(this, 'copyActivitySource', activity)
        this.$set(this, 'copyContent', activity != null)
      })
    },
  },
  methods: {
    refreshCopyActivitySource() {
      navigator.permissions.query({ name: 'clipboard-read' }).then(
        (p) => {
          this.$set(this, 'clipboardPermission', p.state)
          this.$set(this, 'copyActivitySource', null)

          if (p.state == 'granted') {
            navigator.clipboard.readText().then((url) => {
              this.getCopyActivitySource(url).then((activity) => {
                this.$set(this, 'copyActivitySource', activity)
              })
            })
          }
        },
        () => {
          this.clipboardPermission = 'unaccessable'
          console.warn('clipboard permission not requestable')
        }
      )
    },
    async getCopyActivitySource(url) {
      if (url.startsWith(window.location.origin)) {
        url = url.substring(window.location.origin.length)
        let match = router.matcher.match(url)

        if (match.name == 'activity') {
          var scheduleEntry = await this.api
            .get()
            .scheduleEntries({ id: match.params['scheduleEntryId'] })

          return await scheduleEntry.activity()
        }
      }
      return null
    },
    async clearClipboard() {
      await navigator.clipboard.writeText('')
      this.refreshCopyActivitySource()
    },
    cancelCreate() {
      this.close()
    },
    createActivity() {
      const payloadData = {
        ...this.entityData,

        scheduleEntries:
          this.entityData.scheduleEntries
            ?.filter((entry) => !entry.deleted)
            .map((entry) => ({
              period: entry.period()._meta.self,
              start: entry.start,
              end: entry.end,
            })) || [],
      }

      return this.create(payloadData)
    },
    onSuccess(activity) {
      this.close()
      this.$emit('activityCreated', activity)
    },
  },
}
</script>

<style scoped></style>
