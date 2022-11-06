<!--
Displays a single scheduleEntry
-->

<template>
  <content-card
    toolbar
    :loaded="!scheduleEntry()._meta.loading && !activity.camp()._meta.loading"
  >
    <template #title>
      <v-toolbar-title class="font-weight-bold">
        {{ scheduleEntry().number }}
        <v-menu
          v-if="!category._meta.loading"
          offset-y
          :disabled="layoutMode || !isContributor"
        >
          <template #activator="{ on, attrs }">
            <CategoryChip :schedule-entry="scheduleEntry()" v-bind="attrs" v-on="on" />
          </template>
          <v-list>
            <v-list-item
              v-for="cat in camp.categories().items"
              :key="cat._meta.self"
              @click="changeCategory(cat)"
            >
              <v-list-item-title>
                <CategoryChip :category="cat" />
                {{ cat.name }}
              </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
        <a v-if="!editActivityTitle" style="color: inherit" @click="makeTitleEditable()">
          {{ activity.title }}
        </a>
      </v-toolbar-title>
      <div v-if="editActivityTitle" class="mx-2" style="flex-grow: 1">
        <api-text-field
          :uri="activity._meta.self"
          fieldname="title"
          :disabled="layoutMode"
          dense
          autofocus
          :auto-save="false"
          @finished="editActivityTitle = false"
        />
      </div>
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

      <!-- hamburger menu -->
      <v-menu v-if="!layoutMode" offset-y>
        <template #activator="{ on, attrs }">
          <v-btn icon v-bind="attrs" v-on="on">
            <v-icon>mdi-dots-vertical</v-icon>
          </v-btn>
        </template>

        <v-list>
          <DownloadNuxtPdf :config="printConfig" />
          <DownloadReactPdf :config="printConfig" />

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

          <!-- remove activity -->
          <dialog-entity-delete :entity="activity" @submit="onDelete">
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
          </dialog-entity-delete>
        </v-list>
      </v-menu>
    </template>

    <v-card-text class="px-0 py-0">
      <v-skeleton-loader v-if="loading" type="article" />
      <template v-else>
        <!-- Header -->
        <v-row dense class="activity-header">
          <v-col class="col col-sm-6 col-12">
            <v-row v-if="$vuetify.breakpoint.smAndUp" dense>
              <v-col cols="2">
                {{ $tc('entity.scheduleEntry.fields.nr') }}
              </v-col>
              <v-col cols="10">
                {{ $tc('entity.scheduleEntry.fields.time') }}
              </v-col>
            </v-row>
            <v-row
              v-for="scheduleEntryItem in scheduleEntries"
              :key="scheduleEntryItem._meta.self"
              dense
            >
              <v-col cols="2"> ({{ scheduleEntryItem.number }}) </v-col>
              <v-col cols="10">
                {{ rangeShort(scheduleEntryItem.start, scheduleEntryItem.end) }}
              </v-col>
            </v-row>
          </v-col>
          <v-col class="col col-sm-6 col-12">
            <v-row dense>
              <v-col>
                <api-text-field
                  :name="$tc('entity.activity.fields.location')"
                  :uri="activity._meta.self"
                  fieldname="location"
                  :disabled="layoutMode || !isContributor"
                  dense
                />
              </v-col>
            </v-row>
            <v-row dense>
              <v-col>
                <activity-responsibles
                  :activity="activity"
                  :disabled="layoutMode || !isContributor"
                />
              </v-col>
            </v-row>
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
import ContentCard from '@/components/layout/ContentCard.vue'
import ApiTextField from '@/components/form/api/ApiTextField.vue'
import RootNode from '@/components/activity/RootNode.vue'
import ActivityResponsibles from '@/components/activity/ActivityResponsibles.vue'
import { rangeShort } from '@/common/helpers/dateHelperUTCFormatted.js'
import { campRoleMixin } from '@/mixins/campRoleMixin'
import { periodRoute } from '@/router.js'
import DownloadNuxtPdf from '@/components/print/print-nuxt/DownloadNuxtPdfListItem.vue'
import DownloadReactPdf from '@/components/print/print-react/DownloadReactPdfListItem.vue'
import { errorToMultiLineToast } from '@/components/toast/toasts'
import CategoryChip from '@/components/generic/CategoryChip.vue'

export default {
  name: 'ScheduleEntry',
  components: {
    ContentCard,
    ApiTextField,
    RootNode,
    ActivityResponsibles,
    DownloadReactPdf,
    DownloadNuxtPdf,
    CategoryChip,
  },
  mixins: [campRoleMixin],
  provide() {
    return {
      preferredContentTypes: () => this.preferredContentTypes,
      allContentNodes: () => this.contentNodes,
      camp: () => this.camp,
    }
  },
  props: {
    scheduleEntry: {
      type: Function,
      required: true,
    },
  },
  data() {
    return {
      layoutMode: false,
      editActivityTitle: false,
      loading: true,
    }
  },
  computed: {
    activity() {
      return this.scheduleEntry().activity()
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
              scheduleEntry: this.scheduleEntry()._meta.self,
            },
          },
        ],
      }
    },
  },

  // reload data every time user navigates to Activity view
  async mounted() {
    this.loading = true
    await this.scheduleEntry().activity()._meta.load // wait if activity is being loaded as part of a collection
    await this.scheduleEntry().activity().$reload() // reload as single entity to ensure all embedded entities are included in a single network request
    this.loading = false
  },

  methods: {
    rangeShort,
    changeCategory(category) {
      this.activity
        .$patch({
          category: category._meta.self,
        })
        .catch((e) => this.$toast.error(errorToMultiLineToast(e)))
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
    onDelete() {
      // redirect to Picasso
      this.$router.push(periodRoute(this.scheduleEntry().period()))
    },
  },
}
</script>

<style scoped lang="scss">
.activity-header {
  margin-bottom: 0;
  border-bottom: 1px solid rgba(0, 0, 0, 0.12);
  padding: 1.5rem 16px;

  @media #{map-get($display-breakpoints, 'sm-and-down')} {
    border-bottom: none;
  }
}
</style>
