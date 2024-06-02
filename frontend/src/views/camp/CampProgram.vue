<!--
Show all activity schedule entries of a single period.
-->

<template>
  <content-card
    :title="$tc('views.camp.campProgram.title')"
    toolbar
    :no-border="$vuetify.breakpoint.mdAndUp && openFilter"
  >
    <template #title-actions>
      <period-switcher :period="period" />
      <v-spacer />
      <template v-if="$vuetify.breakpoint.smAndUp">
        <v-toolbar-items v-if="isFilterSet">
          <v-chip
            label
            outlined
            :input-value="openFilter"
            color="primary"
            class="align-self-center mr-2"
            @click="openFilter = !openFilter"
          >
            <v-icon left size="20">mdi-filter</v-icon>
            {{ filteredPropertiesCount }}
          </v-chip>
        </v-toolbar-items>
        <v-chip
          v-else
          outlined
          label
          class="mr-1"
          :input-value="openFilter"
          @click="openFilter = !openFilter"
        >
          <v-icon size="20" color="rgba(0, 0, 0, 0.54)">mdi-filter</v-icon>
        </v-chip>
      </template>
      <LockButton
        v-model="editMode"
        :shake="showReminder"
        :disabled-for-guest="!isContributor"
        @click="editMode = !editMode"
      />
      <v-menu offset-y>
        <template #activator="{ on, attrs }">
          <v-btn icon v-bind="attrs" v-on="on">
            <v-badge
              v-if="!$vuetify.breakpoint.smAndUp && filteredPropertiesCount > 0"
              overlap
              offset-x="2"
              dot
            >
              <v-icon>mdi-dots-horizontal</v-icon>
            </v-badge>
            <v-icon v-else>mdi-dots-horizontal</v-icon>
          </v-btn>
        </template>
        <v-list class="py-0">
          <LockUnlockListItem
            v-model="editMode"
            :disabled="!isContributor"
            @click="editMode = !editMode"
          />
          <v-list-item
            :input-value="isFilterSet"
            :color="isFilterSet ? 'primary' : null"
            @click="openFilter = !openFilter"
          >
            <v-list-item-icon>
              <v-icon>mdi-filter</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title>Filter</v-list-item-title>
            </v-list-item-content>
            <v-list-item-action v-if="isFilterSet">
              <v-badge inline color="primary" :content="filteredPropertiesCount" />
            </v-list-item-action>
          </v-list-item>
          <v-divider />
          <DownloadNuxtPdf :config="printConfig" />
          <DownloadClientPdf :config="printConfig" />
        </v-list>
      </v-menu>
    </template>

    <ScheduleEntryFilters
      v-if="$vuetify.breakpoint.mdAndUp && openFilter"
      v-model="filter"
      class="ec-content-card__toolbar--border pb-4 justify-center"
      :loading-endpoints="loadingEndpoints"
      :camp="camp"
      @height-changed="scheduleEntryFiltersHeightChanged"
    />
    <template v-if="loading">
      <v-skeleton-loader type="table" />
    </template>
    <ScheduleEntries
      v-else
      :period="period"
      :show-button="isContributor"
      :match-fn="match"
    >
      <template #default="slotProps">
        <Picasso
          :schedule-entries="slotProps.scheduleEntries"
          :reload="slotProps.reloadEntries"
          :period="period"
          :start="period.start"
          :end="period.end"
          :editable="editMode"
          :is-filter-set="isFilterSet"
          @new-entry="slotProps.on.newEntry"
          @unlock-reminder="showUnlockReminder"
        />
      </template>
    </ScheduleEntries>
    <v-snackbar v-model="showReminder" light class="mb-12">
      <v-icon>mdi-lock</v-icon>
      {{ reminderText }}
    </v-snackbar>
    <v-bottom-sheet v-if="!$vuetify.breakpoint.mdAndUp" v-model="openFilter">
      <v-sheet class="text-center" height="200px">
        <ScheduleEntryFilters
          v-model="filter"
          class="pa-4"
          :loading-endpoints="loadingEndpoints"
          :camp="camp"
        />
      </v-sheet>
    </v-bottom-sheet>
  </content-card>
</template>
<script>
import { campRoleMixin } from '@/mixins/campRoleMixin'
import ContentCard from '@/components/layout/ContentCard.vue'
import Picasso from '@/components/program/picasso/Picasso.vue'
import ScheduleEntries from '@/components/program/ScheduleEntries.vue'
import PeriodSwitcher from '@/components/program/PeriodSwitcher.vue'
import DownloadNuxtPdf from '@/components/print/print-nuxt/DownloadNuxtPdfListItem.vue'
import DownloadClientPdf from '@/components/print/print-client/DownloadClientPdfListItem.vue'
import LockButton from '@/components/generic/LockButton.vue'
import LockUnlockListItem from '@/components/generic/LockUnlockListItem.vue'
import ScheduleEntryFilters from '@/components/program/ScheduleEntryFilters.vue'
import {
  filterAndQueryAreEqual,
  processRouteQuery,
  transformValuesToHalId,
} from '@/helpers/querySyncHelper.js'

export default {
  name: 'CampProgram',
  components: {
    ScheduleEntryFilters,
    DownloadNuxtPdf,
    DownloadClientPdf,
    PeriodSwitcher,
    ContentCard,
    Picasso,
    ScheduleEntries,
    LockButton,
    LockUnlockListItem,
  },
  mixins: [campRoleMixin],
  props: {
    period: { type: Object, required: true },
  },
  data() {
    return {
      showReminder: false,
      reminderText: null,
      openFilter: false,
      loading: true,
      loadingEndpoints: {
        categories: true,
        periods: true,
        campCollaborations: true,
        progressLabels: true,
      },
      filter: {
        category: [],
        responsible: [],
        progressLabel: [],
      },
    }
  },
  computed: {
    camp() {
      return this.period.camp()
    },
    printConfig() {
      return {
        camp: this.camp._meta.self,
        language: this.$store.state.lang.language,
        documentName: this.camp.title + '-picasso.pdf',
        contents: [
          {
            type: 'Picasso',
            options: {
              periods: [this.period._meta.self],
              orientation: 'L',
            },
          },
        ],
      }
    },
    editMode: {
      get() {
        return this.$store.getters.getPicassoEditMode(this.camp._meta.self)
      },
      set(value) {
        this.$store.commit('setPicassoEditMode', {
          campUri: this.camp._meta.self,
          editMode: value,
        })
      },
    },
    filteredPropertiesCount() {
      return Object.values(this.filter).filter((item) =>
        Array.isArray(item) ? item.length : !!item
      ).length
    },
    isFilterSet() {
      return this.filteredPropertiesCount > 0
    },
  },
  watch: {
    openFilter: {
      immediate: true,
      handler: 'openFilterChanged',
    },
    'filter.category': 'persistRouterState',
    'filter.responsible': 'persistRouterState',
    'filter.progressLabel': 'persistRouterState',
  },
  async mounted() {
    await Promise.all([
      this.camp._meta.load,
      this.period.scheduleEntries()._meta.load,
      this.camp.activities()._meta.load,
      this.camp.categories()._meta.load,
      this.period.days()._meta.load,
      this.period.dayResponsibles()._meta.load,
    ])

    this.loading = false

    const queryFilters = processRouteQuery(this.$route.query)
    Object.entries(queryFilters).forEach(([key, value]) => {
      this.filter[key] = value
    })
  },
  methods: {
    showUnlockReminder(move) {
      this.reminderText = move
        ? this.$tc('views.camp.campProgram.reminderLockedMove')
        : this.$tc('views.camp.campProgram.reminderLockedCreate')
      this.showReminder = true
    },
    match(scheduleEntry) {
      return (
        this.filteredPropertiesCount === 0 ||
        ((this.filter.category === null ||
          this.filter.category.length === 0 ||
          this.filter.category.includes(
            scheduleEntry.activity().category()._meta.self
          )) &&
          (this.filter.responsible === null ||
            this.filter.responsible.length === 0 ||
            this.filter.responsible?.every((responsible) =>
              scheduleEntry
                .activity()
                .activityResponsibles()
                .items.map((responsible) => responsible.campCollaboration()._meta.self)
                .includes(responsible)
            ) ||
            (this.filter.responsible[0] === 'none' &&
              scheduleEntry.activity().activityResponsibles().items.length === 0)) &&
          (this.filter.progressLabel === null ||
            this.filter.progressLabel.length === 0 ||
            this.filter.progressLabel?.includes(
              scheduleEntry.activity().progressLabel?.()._meta.self ?? 'none'
            )))
      )
    },
    persistRouterState() {
      const query = transformValuesToHalId(this.filter)
      if (filterAndQueryAreEqual(query, this.$route.query)) return
      this.$router.replace({ query }).catch((err) => console.warn(err))
    },
    openFilterChanged(openFilter) {
      if (!openFilter) {
        this.scheduleEntryFiltersHeightChanged(0)
      }
    },
    scheduleEntryFiltersHeightChanged(h) {
      const root = document.querySelector(':root')
      root.style.setProperty('--schedule-entry-filters-height', `${h}px`)
    },
  },
}
</script>

<style>
:root {
  --schedule-entry-filters-height: 0px;
}
</style>
