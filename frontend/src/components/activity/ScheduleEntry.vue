<!--
Displays a single scheduleEntry
-->

<template>
  <content-card
    class="ec-schedule-entry"
    toolbar
    back
    :loaded="!scheduleEntry._meta.loading && !activity.camp()._meta.loading"
    :max-width="isPaperDisplaySize ? '944px' : ''"
  >
    <template #title>
      <v-toolbar-title class="font-weight-bold">
        <span class="tabular-nums">
          {{ scheduleEntry.number }}
        </span>
        <v-menu
          offset-y
          rounded="lg"
          nudge-left="10"
          nudge-bottom="4"
          :disabled="layoutMode || !isContributor"
        >
          <template #activator="{ on, attrs }">
            <CategoryChip
              :schedule-entry="scheduleEntry"
              large
              dense
              v-bind="attrs"
              v-on="on"
            >
              <template #after>
                <v-icon
                  right
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
          <v-list class="py-0">
            <v-list-item
              v-for="cat in camp.categories().items"
              :key="cat._meta.self"
              class="px-3"
              @click="changeCategory(cat)"
            >
              <v-list-item-title>
                <CategoryChip :category="cat" dense />
                {{ cat.name }}
              </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
        <a v-if="!editActivityTitle" style="color: inherit">
          {{ activity.title }}
        </a>
      </v-toolbar-title>
      <v-btn
        v-if="isContributor && !editActivityTitle"
        icon
        class="ml-1 visible-on-hover"
        width="24"
        height="24"
        @click="makeTitleEditable()"
      >
        <v-icon small>mdi-pencil</v-icon>
      </v-btn>
      <api-form v-if="editActivityTitle" :entity="activity" class="mx-2 flex-grow-1">
        <api-text-field
          path="title"
          :disabled="layoutMode"
          dense
          autofocus
          :auto-save="false"
          @finished="editActivityTitle = false"
        />
      </api-form>
    </template>
    <template #title-actions>
      <!-- layout/content switch (back to content) -->
      <v-btn
        v-if="layoutMode"
        color="success"
        class="ml-3"
        outlined
        @click="layoutMode = false"
      >
        <template v-if="$vuetify.breakpoint.smAndUp">
          <v-icon left>mdi-file-document-edit-outline</v-icon>
          {{ $tc('components.activity.scheduleEntry.backToContents') }}
        </template>
        <template v-else>{{ $tc('global.button.back') }}</template>
      </v-btn>

      <TogglePaperSize v-model="isPaperDisplaySize" />
      <!-- hamburger menu -->
      <v-menu v-if="!layoutMode" offset-y>
        <template #activator="{ on, attrs }">
          <v-btn icon v-bind="attrs" v-on="on">
            <v-icon>mdi-dots-vertical</v-icon>
          </v-btn>
        </template>

        <v-list>
          <DownloadNuxtPdf :config="printConfig" />
          <DownloadClientPdf :config="printConfig" />

          <v-divider />

          <!-- layout/content switch (switch to layout mode) -->
          <v-list-item :disabled="!isContributor" @click="layoutMode = true">
            <v-list-item-icon>
              <v-icon>mdi-puzzle-edit-outline</v-icon>
            </v-list-item-icon>
            <v-list-item-title>
              {{ $tc('components.activity.scheduleEntry.changeLayout') }}
            </v-list-item-title>
          </v-list-item>

          <v-divider />

          <v-list-item @click="copyUrlToClipboard">
            <v-list-item-icon>
              <v-icon>mdi-content-copy</v-icon>
            </v-list-item-icon>
            <v-list-item-title>
              {{ $tc('components.activity.scheduleEntry.copyScheduleEntry') }}
            </v-list-item-title>
          </v-list-item>
          <CopyActivityInfoDialog ref="copyInfoDialog" />

          <v-divider />

          <!-- remove activity -->
          <DialogEntityDelete :entity="activity" @submit="onDelete">
            <template #activator="{ on }">
              <v-list-item :disabled="!isContributor" v-on="on">
                <v-list-item-icon>
                  <v-icon>mdi-delete</v-icon>
                </v-list-item-icon>
                <v-list-item-title>
                  {{ $tc('global.button.delete') }}
                </v-list-item-title>
              </v-list-item>
            </template>
            {{ $tc('components.activity.scheduleEntry.deleteWarning') }}
          </DialogEntityDelete>
        </v-list>
      </v-menu>
    </template>

    <v-card-text class="px-0 py-0">
      <v-skeleton-loader v-if="loading" type="article" />
      <template v-else>
        <!-- Header -->
        <v-row dense class="activity-header">
          <v-col class="col col-sm-6 col-12 px-0 pt-0 d-flex flex-wrap gap-x-4">
            <table>
              <thead>
                <tr>
                  <th scope="col" class="text-right pb-2 pr-4">
                    {{ $tc('entity.scheduleEntry.fields.nr') }}
                  </th>
                  <th scope="col" class="text-left pb-2 pr-4">
                    {{ $tc('entity.scheduleEntry.fields.duration') }}
                  </th>
                  <th scope="col" class="text-left pb-2" colspan="2">
                    {{ $tc('entity.scheduleEntry.fields.time') }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="scheduleEntryItem in scheduleEntries"
                  :key="scheduleEntryItem._meta.self"
                >
                  <th class="text-right tabular-nums pb-2 pr-4">
                    {{ scheduleEntryItem.number }}
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
                    {{ rangeLongEnd(scheduleEntryItem.start, scheduleEntryItem.end) }}
                  </td>
                </tr>
              </tbody>
            </table>
            <DialogActivityEdit
              v-if="activity && isContributor"
              :schedule-entry="scheduleEntry()"
              hide-header-fields
              @activityUpdated="activity.$reload()"
            >
              <template #activator="{ on }">
                <ButtonEdit text small class="v-btn--has-bg" v-on="on" />
              </template>
            </DialogActivityEdit>
          </v-col>
          <v-col class="col col-sm-6 col-12 px-0">
            <api-form :entity="activity" name="activity">
              <v-row dense>
                <v-col class="col col-sm-8 col-12">
                  <api-text-field
                    path="location"
                    :disabled="layoutMode || !isContributor"
                    dense
                  />
                </v-col>
                <v-col class="col col-sm-4 col-12">
                  <api-select
                    path="progressLabel"
                    :items="progressLabels"
                    :disabled="layoutMode || !isContributor"
                    clearable
                    dense
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
import { sortBy } from 'lodash'
import ContentCard from '@/components/layout/ContentCard.vue'
import ApiTextField from '@/components/form/api/ApiTextField.vue'
import RootNode from '@/components/activity/RootNode.vue'
import ActivityResponsibles from '@/components/activity/ActivityResponsibles.vue'
import { dateHelperUTCFormatted } from '@/mixins/dateHelperUTCFormatted.js'
import { campRoleMixin } from '@/mixins/campRoleMixin'
import router, { periodRoute, scheduleEntryRoute } from '@/router.js'
import DownloadNuxtPdf from '@/components/print/print-nuxt/DownloadNuxtPdfListItem.vue'
import DownloadClientPdf from '@/components/print/print-client/DownloadClientPdfListItem.vue'
import { errorToMultiLineToast } from '@/components/toast/toasts'
import CategoryChip from '@/components/generic/CategoryChip.vue'
import CopyActivityInfoDialog from '@/components/activity/CopyActivityInfoDialog.vue'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete.vue'
import TogglePaperSize from '@/components/activity/TogglePaperSize.vue'
import ApiForm from '@/components/form/api/ApiForm.vue'
import ApiSelect from '@/components/form/api/ApiSelect.vue'
import ButtonEdit from '@/components/buttons/ButtonEdit.vue'
import DialogActivityEdit from '@/components/activity/dialog/DialogActivityEdit.vue'

export default {
  name: 'ScheduleEntry',
  components: {
    DialogActivityEdit,
    ButtonEdit,
    ApiForm,
    ApiSelect,
    TogglePaperSize,
    DialogEntityDelete,
    ContentCard,
    ApiTextField,
    RootNode,
    ActivityResponsibles,
    DownloadClientPdf,
    DownloadNuxtPdf,
    CategoryChip,
    CopyActivityInfoDialog,
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
  props: {
    scheduleEntry: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      layoutMode: false,
      editActivityTitle: false,
      categoryChangeState: null,
      loading: true,
    }
  },
  computed: {
    activity() {
      return this.scheduleEntry.activity()
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
        documentName: this.activity.title + '.pdf',
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

  // reload data every time user navigates to Activity view
  async mounted() {
    this.loading = true
    await this.scheduleEntry.activity()._meta.load // wait if activity is being loaded as part of a collection
    this.loading = false

    // to avoid stale data, trigger reload (which includes embedded contentNode data). However, don't await in order to render early with cached data.
    this.scheduleEntry.activity().$reload()
  },

  methods: {
    changeCategory(category) {
      this.categoryChangeState = 'saving'
      this.activity
        .$patch({
          category: category._meta.self,
        })
        .catch((e) => this.$toast.error(errorToMultiLineToast(e)))
        .then(() => (this.categoryChangeState = null))
        .catch((e) => {
          this.categoryChangeState = 'error'
          this.$toast.error(errorToMultiLineToast(e))
        })
    },
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

      this.$toast.info(
        this.$tc('global.toast.copied', null, { source: this.activityName }),
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

:deep(.ec-content-card__toolbar:not(:hover) button.visible-on-hover:not(:focus)) {
  opacity: 0;
}

:deep(.ec-content-card__toolbar button.visible-on-hover) {
  opacity: 1;
  transition: opacity 0.2s linear;
}

.e-category-chip-save-icon {
  font-size: 18px;
}

.ec-schedule-entry {
  transition: max-width 0.7s ease;
}
</style>
