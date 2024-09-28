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
      <CopyActivityInfoDialog @closed="refreshCopyActivitySource">
        <template #activator="{ on }">
          <v-btn v-show="clipboardPermission === 'prompt'" v-on="on">
            <v-icon left>mdi-information-outline</v-icon>
            {{ $tc('components.program.dialogActivityCreate.copyPasteActivity') }}
          </v-btn>
        </template>
      </CopyActivityInfoDialog>
    </template>

    <div v-if="hasCopyActivitySource">
      <div class="mb-8">
        <div v-if="!clipboardAccessDenied">
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
    <DialogActivityForm :activity="entityData" :period="period" :autoselect-title="true">
      <template v-if="clipboardAccessDenied" #textFieldTitleAppend>
        <PopoverPrompt
          v-model="copyActivitySourceUrlShowPopover"
          icon="mdi-content-paste"
          :title="$tc('components.program.dialogActivityCreate.pasteActivity')"
        >
          <template #activator="scope">
            <v-btn
              :title="$tc('components.program.dialogActivityCreate.pasteActivity')"
              text
              class="v-btn--has-bg"
              height="56"
              v-on="scope.on"
            >
              <v-progress-circular v-if="copyActivitySourceUrlLoading" indeterminate />
              <v-icon v-else>mdi-content-paste</v-icon>
            </v-btn>
          </template>
          {{ $tc('components.program.dialogActivityCreate.copySourceInfo') }}
          <e-text-field
            v-model="copyActivitySourceUrl"
            :label="$tc('components.program.dialogActivityCreate.copyActivity')"
            style="margin-bottom: 12px"
            autofocus
          />
        </PopoverPrompt>
      </template>
    </DialogActivityForm>
  </dialog-form>
</template>

<script>
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogBase from '@/components/dialog/DialogBase.vue'
import DialogActivityForm from './DialogActivityForm.vue'
import CopyActivityInfoDialog from '@/components/activity/CopyActivityInfoDialog.vue'
import PopoverPrompt from '@/components/prompt/PopoverPrompt.vue'
import { uniqueId } from 'lodash'
import router from '@/router.js'
import CategoryChip from '@/components/generic/CategoryChip.vue'

export default {
  name: 'DialogActivityCreate',
  components: {
    CategoryChip,
    DialogForm,
    DialogActivityForm,
    CopyActivityInfoDialog,
    PopoverPrompt,
  },
  extends: DialogBase,
  props: {
    scheduleEntry: { type: Object, required: true },

    // currently visible period
    period: { type: Object, required: true },
  },
  data() {
    return {
      clipboardPermission: 'unknown',
      copyActivitySource: null,
      copyActivitySourceUrl: null,
      copyActivitySourceUrlLoading: false,
      copyActivitySourceUrlShowPopover: false,
      entityProperties: ['title', 'location', 'scheduleEntries'],
      embeddedEntities: ['category'],
      entityUri: '',
    }
  },
  computed: {
    camp() {
      return this.period.camp()
    },
    clipboardAccessDenied() {
      return (
        this.clipboardPermission === 'unaccessable' ||
        this.clipboardPermission === 'denied'
      )
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
          this.entityData.location = this.copyActivitySource.location

          const sourceCamp = this.copyActivitySource.camp()
          const sourceCategory = this.copyActivitySource.category()

          if (this.camp._meta.self === sourceCamp._meta.self) {
            // same camp; use came category
            this.entityData.category = sourceCategory._meta.self
          } else {
            // different camp; use category with same short-name
            const categories = this.camp
              .categories()
              .allItems.filter((c) => c.short === sourceCategory.short)

            if (categories.length === 1) {
              this.entityData.category = categories[0]._meta.self
            }
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
          title: this.entityData?.title,
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
        // clear the variable parts of the form on exit
        this.copyActivitySource = null
        this.copyActivitySourceUrl = null
        this.entityData.location = ''
        this.entityData.scheduleEntries = []
      }
    },
    copyActivitySourceUrl: function (url) {
      this.copyActivitySourceUrlLoading = true

      this.getCopyActivitySource(url).then(
        (activityProxy) => {
          if (activityProxy != null) {
            activityProxy._meta.load.then(
              (activity) => {
                this.copyActivitySource = activity
                this.copyContent = true
                this.copyActivitySourceUrlLoading = false
              },
              () => {
                this.copyActivitySourceUrlLoading = false
              }
            )
          } else {
            this.copyActivitySource = null
            this.copyContent = false
            this.copyActivitySourceUrlLoading = false
          }

          // if Paste-Popover is shown, close it now
          if (this.copyActivitySourceUrlShowPopover) {
            this.$nextTick(() => {
              this.copyActivitySourceUrlShowPopover = false
            })
          }
        },
        () => {
          this.copyActivitySourceUrlLoading = false
        }
      )
    },
  },
  mounted() {
    this.api.href(this.api.get(), 'activities').then((url) => (this.entityUri = url))
  },
  methods: {
    refreshCopyActivitySource() {
      navigator.permissions.query({ name: 'clipboard-read' }).then(
        (p) => {
          this.clipboardPermission = p.state
          this.copyActivitySource = null

          if (p.state === 'granted') {
            navigator.clipboard
              .readText()
              .then(async (url) => {
                const copyActivitySource = await this.getCopyActivitySource(url)
                this.copyActivitySource = await copyActivitySource?._meta.load
              })
              .catch(() => {
                this.clipboardPermission = 'unaccessable'
                console.warn('clipboard permission not requestable')
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
      if (url?.startsWith(window.location.origin)) {
        url = url.substring(window.location.origin.length)
        const match = router.matcher.match(url)

        if (match.name === 'camp/activity') {
          const scheduleEntry = await this.api
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
      this.$emit('activity-created', activity)
    },
  },
}
</script>

<style scoped></style>
