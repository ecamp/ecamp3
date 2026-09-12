<template>
  <dialog-form
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-calendar-plus"
    :title="$t('entity.activity.new')"
    :submit-action="createActivity"
    :submit-label="$t('global.button.create')"
    submit-icon="mdi-plus"
    submit-color="success"
    :cancel-action="cancelCreate"
    max-width="700px"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>

    <template #moreActions>
      <ClipboardInfoDialog
        translation-context-i18n-key="components.program.dialogActivityCreate.clipboardInfoDialog"
        @closed="attemptLoadingEntityFromClipboard"
      >
        <template #activator="{ props }">
          <v-btn v-show="showClipboardPrompt" v-bind="props">
            <v-icon start>mdi-information-outline</v-icon>
            {{ $t('components.program.dialogActivityCreate.copyPasteActivity') }}
          </v-btn>
        </template>
      </ClipboardInfoDialog>
    </template>

    <div v-if="hasClipboardEntity">
      <div class="mb-8">
        <div v-if="!clipboardAccessDenied">
          {{ $t('components.program.dialogActivityCreate.clipboard') }}
          <div style="float: right">
            <small>
              <a
                href="#"
                style="color: inherit; text-decoration: none"
                @click="clearClipboard"
              >
                {{ $t('components.program.dialogActivityCreate.clearClipboard') }}
              </a>
            </small>
          </div>
        </div>
        <v-list-item
          class="ec-copy-source rounded-xl bg-blue-grey-lighten-5 text-blue-grey-darken-4 mt-1"
        >
          <template #prepend>
            <v-avatar>
              <v-icon color="blue-grey">mdi-clipboard-check-outline</v-icon>
            </v-avatar>
          </template>

          <v-list-item-title>
            <CategoryChip :category="clipboardEntity.category()" class="mx-1" dense />
            {{ clipboardEntity.title }}
          </v-list-item-title>
          <v-list-item-subtitle>
            {{ clipboardEntity.camp().title }}
          </v-list-item-subtitle>

          <template #append>
            <v-list-item-action>
              <e-checkbox
                v-model="copyContent"
                :label="$t('components.program.dialogActivityCreate.copyActivityContent')"
              />
            </v-list-item-action>
          </template>
        </v-list-item>
      </div>
    </div>
    <DialogActivityForm
      :activity="entityData"
      :autoselect-title="true"
      :period="scheduleEntry.period()"
    >
      <template v-if="clipboardAccessDenied" #textFieldTitleAppend>
        <PopoverPrompt
          v-model="showCopyActivityUrlPopover"
          icon="mdi-content-paste"
          :title="$t('components.program.dialogActivityCreate.pasteActivity')"
        >
          <template #activator="{ props }">
            <v-btn
              v-bind="props"
              :title="$t('components.program.dialogActivityCreate.pasteActivity')"
              variant="text"
              class="v-btn--has-bg"
              height="56"
            >
              <v-progress-circular v-if="clipboardEntityLoading" indeterminate />
              <v-icon v-else>mdi-content-paste</v-icon>
            </v-btn>
          </template>
          {{ $t('components.program.dialogActivityCreate.copySourceInfo') }}
          <e-text-field
            v-model="clipboardEntityUrl"
            :label="$t('components.program.dialogActivityCreate.copyActivity')"
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
import DialogActivityForm from '@/components/activity/dialog/DialogActivityForm.vue'
import ClipboardInfoDialog from '../generic/ClipboardInfoDialog.vue'
import PopoverPrompt from '@/components/prompt/PopoverPrompt.vue'
import { uniqueId } from 'lodash-es'
import CategoryChip from '@/components/generic/CategoryChip.vue'
import { useClipboardEntity } from '../generic/useClipboardEntity.js'
import router from '@/router.js'
import { apiStore as api } from '@/plugins/store'
import { getCurrentInstance, nextTick, ref } from 'vue'

export default {
  name: 'DialogActivityCreate',
  components: {
    CategoryChip,
    DialogForm,
    DialogActivityForm,
    ClipboardInfoDialog,
    PopoverPrompt,
  },
  extends: DialogBase,
  props: {
    scheduleEntry: { type: Object, required: true },
  },
  emits: ['activityCreated'],
  setup() {
    const showCopyActivityUrlPopover = ref(false)

    // Hack: In this case we need access to a method defined in the options API
    // because moving this method to the composition API would force us to move
    // entityData and a whole big mess of inheritance-related code to the
    // composition API as well. On a previous attempt, this completely broke the
    // vee-validate validators, resulting in all validation always failing in all
    // dialogs in the app. So using the undocumented but well-known
    // getCurrentInstance here is the lesser evil right now.
    const currentInstance = getCurrentInstance()

    const clipboard = useClipboardEntity({
      fetchClipboardEntity: async (url) => {
        if (!url.startsWith(window.location.origin)) return null
        url = url.substring(window.location.origin.length)
        const match = router.resolve(url)

        if (match.name !== 'camp/activity') return null
        const result = await api.get().activities({ id: match.params['activityId'] })

        // if Paste-Popover is shown, close it now
        if (showCopyActivityUrlPopover.value) {
          nextTick(() => {
            showCopyActivityUrlPopover.value = false
          })
        }

        return result
      },
      onEntityLoaded: function () {
        currentInstance.proxy.setCopyContentCheckbox(true)
      },
      onEntityLoadFailed: function () {
        currentInstance.proxy.setCopyContentCheckbox(false)
      },
    })

    return {
      ...clipboard,
      showCopyActivityUrlPopover,
    }
  },
  data() {
    return {
      entityProperties: ['title', 'location', 'scheduleEntries'],
      embeddedEntities: ['category'],
      entityUri: '',
    }
  },
  computed: {
    camp() {
      return this.period.camp()
    },
    period() {
      return this.scheduleEntry.period()
    },
    copyContent: {
      get() {
        return this.entityData.copyActivitySource != null
      },
      set(val) {
        this.setCopyContentCheckbox(val)
      },
    },
  },
  watch: {
    showDialog: function (showDialog) {
      if (showDialog) {
        this.attemptLoadingEntityFromClipboard()
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
        })
      } else {
        // clear the variable parts of the form on exit
        this.clipboardEntityUrl = null
        this.entityData.location = ''
        this.entityData.scheduleEntries = []
      }
      this.clipboardEntity = null
    },
  },
  mounted() {
    this.api.href(this.api.get(), 'activities').then((url) => (this.entityUri = url))
  },
  methods: {
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
    // noinspection JSUnusedGlobalSymbols
    onSuccess(activity) {
      const scheduleEntries = activity.scheduleEntries().items
      if (scheduleEntries.length > 0) {
        const firstPeriodIri = scheduleEntries[0].period()._meta.self
        this.api.reload(this.api.get(firstPeriodIri).scheduleEntries())
        const reloadedPeriods = new Set([firstPeriodIri])
        scheduleEntries
          .filter((entry) => typeof entry.period === 'function')
          .forEach((entry) => {
            const targetPeriodUri = entry.period()._meta.self
            if (!reloadedPeriods.has(targetPeriodUri)) {
              reloadedPeriods.add(targetPeriodUri)
              this.api.reload(this.api.get(targetPeriodUri).scheduleEntries())
            }
          })
      }
      this.close()
      this.$emit('activityCreated', activity)
    },
    setCopyContentCheckbox(val) {
      if (val) {
        this.entityData.copyActivitySource = this.clipboardEntity._meta.self
        this.entityData.title = this.clipboardEntity.title
        this.entityData.location = this.clipboardEntity.location

        const sourceCamp = this.clipboardEntity.camp()
        const sourceCategory = this.clipboardEntity.category()

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
}
</script>

<style scoped></style>
