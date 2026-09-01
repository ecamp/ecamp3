<!--
Displays a single scheduleEntry
-->

<template>
  <content-card
    :key="activityId"
    class="ec-schedule-entry"
    toolbar
    :back="backRoute"
    :loaded="!scheduleEntry._meta.loading && !activity.camp()._meta.loading"
    :max-width="isPaperDisplaySize ? '944px' : ''"
  >
    <template #title>
      <h1 class="font-weight-bold text-h6 d-flex gap-1 sm:gap-2 items-center truncate">
        <span v-if="scheduleEntry.number !== ''" class="tabular-nums">
          {{ scheduleEntry.number }}
        </span>
        <v-menu
          offset-y
          location="bottom"
          offset="10 4"
          :disabled="layoutMode || !isContributor"
        >
          <template #activator="{ props }">
            <CategoryChip
              :schedule-entry="scheduleEntry"
              class="flex-shrink-0"
              large
              dense
              v-bind="props"
            >
              <template #after>
                <v-icon
                  v-if="isContributor"
                  end
                  class="ml-0 e-category-chip-save-icon"
                  :class="{ 'mdi-spin': categoryChangeState === 'saving' }"
                >
                  <template v-if="categoryChangeState === 'saving'"
                    >mdi-autorenew
                  </template>
                  <template v-else-if="categoryChangeState === 'error'"
                    >mdi-alert
                  </template>
                  <template v-else>mdi-chevron-down</template>
                </v-icon>
              </template>
            </CategoryChip>
          </template>
          <v-list class="py-0" rounded="lg">
            <v-list-item
              v-for="cat in camp.categories().items"
              :key="cat._meta.self"
              class="px-3"
              :title="cat.name"
              @click="changeCategory(cat)"
            >
              <template #prepend>
                <CategoryChip :category="cat" dense class="mr-2" />
              </template>
            </v-list-item>
          </v-list>
        </v-menu>
        <span v-if="!editActivityTitle" class="flex-shrink truncate">
          {{ activity.title }}
        </span>
      </h1>
      <v-btn
        v-if="isContributor && !editActivityTitle"
        icon
        class="d-none d-sm-block ml-1 visible-on-hover"
        width="24"
        height="24"
        @click="makeTitleEditable()"
      >
        <v-icon size="x-small">mdi-pencil</v-icon>
      </v-btn>
      <api-form v-if="editActivityTitle" :entity="activity" class="mx-2 flex-grow-10">
        <api-text-field
          path="title"
          :disabled="layoutMode"
          density="compact"
          autofocus
          :auto-save="false"
          @finished="editActivityTitle = false"
          @keydown.esc="editActivityTitle = false"
        />
      </api-form>
    </template>
    <template #title-actions>
      <!-- layout/content switch (back to content) -->
      <v-btn
        v-if="layoutMode"
        color="success"
        class="ml-3"
        variant="outlined"
        @click="layoutMode = false"
      >
        <template v-if="$vuetify.display.smAndUp">
          <v-icon start>mdi-file-document-edit-outline</v-icon>
          {{ $t('components.activity.scheduleEntry.backToContents') }}
        </template>
        <template v-else>{{ $t('global.button.back') }}</template>
      </v-btn>

      <TogglePaperSize v-model="isPaperDisplaySize" />
      <CommentsToggleButton v-if="!layoutMode" />
      <!-- hamburger menu -->
      <v-menu v-if="!layoutMode" offset-y>
        <template #activator="{ props }">
          <v-btn icon="mdi-dots-vertical" v-bind="props" />
        </template>

        <v-list>
          <DownloadNuxtPdf :config="printConfig" />
          <DownloadClientPdf :config="printConfig" />

          <v-divider v-if="!isOutsider" />

          <v-list-item
            v-if="isContributor"
            :title="$t('global.button.rename')"
            prepend-icon="mdi-pencil"
            @click="makeTitleEditable()"
          />

          <!-- layout/content switch (switch to layout mode) -->
          <v-list-item
            v-if="!isOutsider"
            :title="$t('components.activity.scheduleEntry.changeLayout')"
            prepend-icon="mdi-puzzle-edit-outline"
            :disabled="!isContributor"
            @click="layoutMode = true"
          />

          <v-divider />

          <v-list-item
            :title="$t('components.activity.scheduleEntry.copyScheduleEntry')"
            prepend-icon="mdi-content-copy"
            @click="copyUrlToClipboard"
          />
          <ClipboardInfoDialog
            ref="copyInfoDialog"
            translation-context-i18n-key="components.activity.scheduleEntry.clipboardInfoDialog"
          />

          <v-divider v-if="!isOutsider" />

          <!-- remove activity -->
          <DialogEntityDelete v-if="!isOutsider" :entity="activity" @submit="onDelete">
            <template #activator="{ props }">
              <v-list-item
                :title="$t('global.button.delete')"
                prepend-icon="mdi-delete"
                :disabled="!isContributor"
                v-bind="props"
              />
            </template>
            {{ $t('components.activity.scheduleEntry.deleteWarning') }}
          </DialogEntityDelete>
        </v-list>
      </v-menu>
    </template>

    <v-card-text class="px-0 py-0">
      <v-skeleton-loader v-if="loading" type="article" />
      <template v-else>
        <!-- Header -->
        <v-row dense class="activity-header">
          <v-col
            cols="12"
            sm="6"
            class="px-0 pt-0 d-flex flex-wrap gap-x-4 align-content-start gap-y-1"
          >
            <table>
              <thead>
                <tr>
                  <th
                    v-if="category.numberingStyle !== '-'"
                    scope="col"
                    class="text-right pb-2 pr-4"
                  >
                    {{ $t('entity.scheduleEntry.fields.nr') }}
                  </th>
                  <th scope="col" class="text-left pb-2 pr-4">
                    {{ $t('entity.scheduleEntry.fields.duration') }}
                  </th>
                  <th scope="col" class="text-left pb-2" colspan="2">
                    {{ $t('entity.scheduleEntry.fields.time') }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="scheduleEntryItem in scheduleEntries"
                  :key="scheduleEntryItem._meta.self"
                >
                  <th
                    v-if="category.numberingStyle !== '-'"
                    class="text-right tabular-nums pb-2 pr-4"
                  >
                    <RouterLink
                      v-if="scheduleEntryItem._meta.self !== scheduleEntry._meta.self"
                      :to="scheduleEntryRoute(scheduleEntryItem)"
                      class="e-title-link"
                    >
                      {{ scheduleEntryItem.number }}
                    </RouterLink>
                    <template v-else>
                      {{ scheduleEntryItem.number }}
                    </template>
                  </th>
                  <td class="text-left tabular-nums pb-2 pr-4">
                    {{
                      timeDurationShort(scheduleEntryItem.start, scheduleEntryItem.end)
                    }}
                  </td>
                  <td class="text-right tabular-nums pb-2 pr-1">
                    {{ dateShort(scheduleEntryItem.start) }}
                  </td>
                  <td class="text-left tabular-nums pb-2 pr-0">
                    <RouterLink
                      v-if="
                        category.numberingStyle === '-' &&
                        scheduleEntryItem._meta.self !== scheduleEntry._meta.self
                      "
                      :to="scheduleEntryRoute(scheduleEntryItem)"
                      class="e-title-link"
                    >
                      {{ rangeLongEnd(scheduleEntryItem.start, scheduleEntryItem.end) }}
                    </RouterLink>
                    <template v-else>
                      {{ rangeLongEnd(scheduleEntryItem.start, scheduleEntryItem.end) }}
                    </template>
                  </td>
                </tr>
              </tbody>
            </table>
            <DialogActivityEdit
              v-if="scheduleEntry && isContributor"
              :schedule-entry="scheduleEntry"
              hide-header-fields
              @activity-updated="activity.$reload()"
            >
              <template #activator="{ props }">
                <ButtonEdit variant="tonal" size="small" v-bind="props" />
              </template>
            </DialogActivityEdit>
          </v-col>
          <v-col cols="12" sm="6" class="px-0">
            <api-form :entity="activity" name="activity">
              <v-row dense>
                <v-col sm="8" cols="12">
                  <api-text-field
                    path="location"
                    :disabled="layoutMode || !isContributor"
                    density="compact"
                  />
                </v-col>
                <v-col sm="4" cols="12">
                  <api-select
                    path="progressLabel"
                    :items="progressLabels"
                    :disabled="layoutMode || !isContributor"
                    clearable
                    density="compact"
                  />
                </v-col>
              </v-row>
              <v-row dense>
                <v-col>
                  <ActivityResponsibles
                    :activity="activity"
                    :disabled="layoutMode || !isContributor"
                  />
                </v-col>
              </v-row>
            </api-form>
          </v-col>
        </v-row>

        <v-skeleton-loader v-if="loading" type="article" />
        <root-node
          v-else
          :content-node="activity.rootContentNode()"
          :layout-mode="layoutMode"
          :disabled="isContributor === false"
        />
      </template>
    </v-card-text>
  </content-card>
</template>

<script>
import { sortBy } from 'lodash-es'
import ContentCard from '@/components/layout/ContentCard.vue'
import ApiTextField from '@/components/form/api/ApiTextField.vue'
import RootNode from '@/components/activity/RootNode.vue'
import ActivityResponsibles from '@/components/activity/ActivityResponsibles.vue'
import { dateHelperUTCFormatted } from '@/mixins/dateHelperUTCFormatted.js'
import { campRoleMixin } from '@/mixins/campRoleMixin'
import router, {
  campRoute,
  firstActivityScheduleEntry,
  periodRoute,
  scheduleEntryRoute,
} from '@/router.js'
import DownloadNuxtPdf from '@/components/print/print-nuxt/DownloadNuxtPdfListItem.vue'
import DownloadClientPdf from '@/components/print/print-client/DownloadClientPdfListItem.vue'
import { errorToMultiLineToast } from '@/components/toast/toasts'
import CategoryChip from '@/components/generic/CategoryChip.vue'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete.vue'
import TogglePaperSize from '@/components/activity/TogglePaperSize.vue'
import CommentsToggleButton from '@/components/comments/CommentsToggleButton.vue'
import ApiForm from '@/components/form/api/ApiForm.vue'
import ApiSelect from '@/components/form/api/ApiSelect.vue'
import ButtonEdit from '@/components/buttons/ButtonEdit.vue'
import DialogActivityEdit from '@/components/activity/dialog/DialogActivityEdit.vue'
import scheduleEntryRouteChange from '@/helpers/scheduleEntryRouteChange.js'
import ClipboardInfoDialog from '../generic/ClipboardInfoDialog.vue'
import { useToast } from 'vue-toastification'

export default {
  name: 'ScheduleEntry',
  components: {
    DialogActivityEdit,
    ButtonEdit,
    ApiForm,
    ApiSelect,
    TogglePaperSize,
    CommentsToggleButton,
    DialogEntityDelete,
    ContentCard,
    ApiTextField,
    RootNode,
    ActivityResponsibles,
    DownloadClientPdf,
    DownloadNuxtPdf,
    CategoryChip,
    ClipboardInfoDialog,
  },
  mixins: [campRoleMixin, dateHelperUTCFormatted],
  provide() {
    return {
      preferredContentTypes: () => this.preferredContentTypes,
      allContentNodes: () => this.contentNodes,
      camp: this.camp,
      isPaperDisplaySize: () => this.isPaperDisplaySize,
    }
  },
  async beforeRouteUpdate(to, from, next) {
    return scheduleEntryRouteChange(this.activityId, to, from, next)
  },
  props: {
    activityId: {
      type: String,
      required: true,
    },
    scheduleEntryId: {
      type: String,
      default: null,
    },
  },
  setup() {
    const toast = useToast()
    return { toast }
  },
  data() {
    return {
      layoutMode: false,
      editActivityTitle: false,
      categoryChangeState: null,
      scheduleEntry: null,
      loading: true,
    }
  },
  head() {
    return {
      title: () =>
        `${this.scheduleEntry.number ? this.scheduleEntry.number + ' ' : ''}[${this.category.short}]: ${this.activity.title}`,
      templateParams: {
        section: this.camp.shortTitle,
      },
    }
  },
  computed: {
    backRoute() {
      return (
        window.history.state?.activityBack ||
        (this.scheduleEntry?.period
          ? periodRoute(this.scheduleEntry.period())
          : campRoute(this.camp, 'program'))
      )
    },
    activity() {
      return this.api.get().activities({ id: this.activityId })
    },
    camp() {
      return this.activity.camp()
    },
    category() {
      return this.activity.category()
    },
    scheduleEntries() {
      return this.activity.scheduleEntries().items
    },
    activityName() {
      return (
        (this.scheduleEntry.number ? this.scheduleEntry.number + ' ' : '') +
        (this.category.short ? this.category.short + ': ' : '') +
        this.activity.title
      )
    },
    progressLabels() {
      const labels = sortBy(this.camp.progressLabels().items, (l) => l.position)
      return labels.map((label) => ({
        value: label._meta.self,
        text: label.title,
      }))
    },
    contentNodes() {
      return this.activity.contentNodes()
    },
    preferredContentTypes() {
      return this.category.preferredContentTypes()
    },
    printConfig() {
      return {
        camp: this.camp._meta.self,
        language: this.$store.state.lang.language,
        documentName: this.activity.title,
        options: { pageNumbers: false },
        contents: [
          {
            type: 'Activity',
            options: {
              activity: this.activity._meta.self,
              scheduleEntry: this.scheduleEntry._meta.self,
            },
          },
        ],
      }
    },
    isPaperDisplaySize: {
      get() {
        return this.$store.getters.getPaperDisplaySize(this.camp._meta.self)
      },
      set(value) {
        this.$store.commit('setPaperDisplaySize', {
          campUri: this.camp._meta.self,
          paperDisplaySize: value,
        })
      },
    },
  },

  watch: {
    scheduleEntryId: {
      async handler() {
        await this.loadScheduleEntry()
      },
      immediate: true,
    },
  },

  // reload data every time user navigates to Activity view
  async mounted() {
    this.loading = true
    await this.scheduleEntry.activity()._meta.load // wait if activity is being loaded as part of a collection
    this.loading = false
    // no refresh of activity here because the requireActivityScheduleEntry guard already does a refresh
  },

  methods: {
    async loadScheduleEntry() {
      try {
        this.scheduleEntry = this.api.get().scheduleEntries({ id: this.scheduleEntryId })
      } catch {
        this.scheduleEntry = await firstActivityScheduleEntry(this.activityId)
      }
    },
    changeCategory(category) {
      this.categoryChangeState = 'saving'
      this.activity
        .$patch({
          category: category._meta.self,
        })
        .catch((e) => this.toast.error(errorToMultiLineToast(e)))
        .then(() => {
          this.categoryChangeState = null
          if (category.numberingStyle !== this.scheduleEntry.numberingStyle) {
            // When changing numbering styles, the schedule entry numbers of all schedule
            // entries in the whole period may change
            this.reloadAllScheduleEntriesInRelatedPeriods()
          }
        })
        .catch((e) => {
          this.categoryChangeState = 'error'
          this.toast.error(errorToMultiLineToast(e))
        })
    },
    async reloadAllScheduleEntriesInRelatedPeriods() {
      const periods = [
        ...new Set(
          this.activity.scheduleEntries().items.map((scheduleEntry) => {
            return scheduleEntry.period()
          })
        ),
      ]
      await Promise.all(
        periods.map(async (period) => {
          period.scheduleEntries().$reload()
        })
      )
      this.loadScheduleEntry()
    },
    scheduleEntryRoute,
    countContentNodes(contentType) {
      return this.contentNodes.items.filter((cn) => {
        return cn.contentType().id === contentType.id
      }).length
    },
    makeTitleEditable() {
      if (this.isContributor) {
        this.editActivityTitle = true
      }
    },
    async copyUrlToClipboard() {
      try {
        const res = await navigator.permissions.query({ name: 'clipboard-read' })
        if (res.state === 'prompt') {
          this.$refs.copyInfoDialog.open()
        }
      } catch {
        console.warn('clipboard permission not requestable')
      }

      const scheduleEntry = scheduleEntryRoute(this.scheduleEntry)
      const url = window.location.origin + router.resolve(scheduleEntry).href
      await navigator.clipboard.writeText(url)

      this.toast.info(
        this.$t('global.toast.copied', { source: this.activityName }, null),
        {
          timeout: 2000,
        }
      )
    },
    onDelete() {
      // redirect to Picasso
      this.$router.push(periodRoute(this.scheduleEntry.period()))
    },
  },
}
</script>

<style scoped lang="scss">
.activity-header {
  margin-bottom: 0;
  border-bottom: 1px solid rgba(0, 0, 0, 0.12);
  padding: 1.5rem 16px;
}

.e-category-chip-save-icon {
  font-size: 18px;
}

.ec-schedule-entry {
  transition: max-width 0.7s ease;
}
</style>
