<!--
Show all activity schedule entries of a single period.
-->

<template>
  <content-card
    :title="$t('views.camp.campProgram.title')"
    toolbar
    :no-border="$vuetify.display.mdAndUp && openFilter"
  >
    <template #title-actions>
      <period-switcher :period="period" />
      <v-spacer />
      <template v-if="$vuetify.display.smAndUp">
        <v-toolbar-items v-if="isFilterSet">
          <v-chip
            label
            variant="outlined"
            color="primary"
            class="align-self-center mr-1"
            @click="openFilter = !openFilter"
          >
            <v-icon start size="20">mdi-filter</v-icon>
            {{ filteredPropertiesCount }}
          </v-chip>
        </v-toolbar-items>
        <v-chip
          v-else
          border="sm"
          color="surface"
          variant="flat"
          label
          class="mr-1"
          @click="openFilter = !openFilter"
        >
          <v-icon size="20" color="rgba(0, 0, 0, 0.54)">mdi-filter</v-icon>
        </v-chip>
      </template>
      <LockButton
        v-if="!isOutsider"
        v-model="editMode"
        :shake="showReminder"
        :disabled-for-guest="!isContributor"
        class="mr-n1"
        @click="editMode = !editMode"
      />
      <v-menu offset-y>
        <template #activator="{ props }">
          <v-btn icon size="small" v-bind="props" data-testid="campprogram-menu">
            <v-icon size="large">mdi-dots-horizontal</v-icon>
          </v-btn>
        </template>
        <v-list class="py-0">
          <LockUnlockListItem
            v-if="!isOutsider"
            v-model="editMode"
            :disabled="!isContributor"
            @click="editMode = !editMode"
          />
          <v-list-item
            title="Filter"
            prepend-icon="mdi-filter"
            :active="isFilterSet"
            :color="isFilterSet ? 'primary' : null"
            @click="openFilter = !openFilter"
          >
            <template v-if="isFilterSet" #append>
              <v-badge inline color="primary" :content="filteredPropertiesCount" />
            </template>
          </v-list-item>
          <v-divider />
          <DownloadNuxtPdf :config="printConfig" />
          <DownloadClientPdf :config="printConfig" />
        </v-list>
      </v-menu>
    </template>

    <ScheduleEntryFilters
      v-if="$vuetify.display.mdAndUp && openFilter"
      v-model="filter"
      class="ec-content-card__toolbar--border pb-4 justify-center"
      :loading-endpoints="loadingEndpoints"
      :camp="camp"
      :filter-fn="filterFn"
      :hide-self-filter="isOutsider"
      hide-period-filter
      hide-day-filter
      @height-changed="scheduleEntryFiltersHeightChanged"
    />
    <template v-if="loading">
      <v-skeleton-loader type="table" class="ma-4" />
    </template>
    <ScheduleEntries
      v-else
      :period="period"
      :show-button="isContributor"
      :filter-fn="filterMatchScheduleEntry"
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
    <v-snackbar v-model="showReminder" :timeout="REMINDER_TIMEOUT" light class="mb-12">
      <v-icon>mdi-lock</v-icon>
      {{ reminderText }}
    </v-snackbar>
    <v-bottom-sheet v-if="!$vuetify.display.mdAndUp" v-model="openFilter">
      <v-sheet class="text-center" height="200px">
        <ScheduleEntryFilters
          v-model="filter"
          class="pa-4"
          :loading-endpoints="loadingEndpoints"
          :camp="camp"
          hide-period-filter
          hide-day-filter
          :filter-fn="filterFn"
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
import { filterMatchScheduleEntry } from '@/common/helpers/filterMatchScheduleEntry.js'
import campShortTitle from '@/common/helpers/campShortTitle.js'

const REMINDER_TIMEOUT = 5000

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
      REMINDER_TIMEOUT,
      reminderInst: null,
      reminderText: null,
      openFilter: false,
      loading: true,
      loadingEndpoints: {
        categories: true,
        periods: true,
        days: false,
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
  head() {
    return {
      title: () =>
        this.$t('views.camp.campProgram.title') + ': ' + this.period.description,
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
        documentName: campShortTitle(this.camp) + '-' + this.period.description,
        options: { pageNumbers: false },
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
    filterMatchScheduleEntry() {
      return (scheduleEntry) => filterMatchScheduleEntry(scheduleEntry, this.filter)
    },
    filterFn() {
      return (filter) =>
        this.period
          .scheduleEntries()
          .items.filter((scheduleEntry) =>
            filterMatchScheduleEntry(scheduleEntry, filter)
          )
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
    const queryFilters = processRouteQuery(this.$route.query)
    Object.entries(queryFilters).forEach(([key, value]) => {
      this.filter[key] = value
    })

    await Promise.all([
      this.camp._meta.load,
      this.period.scheduleEntries()._meta.load,
      this.camp.activities()._meta.load,
      this.loadEndpointData('categories', 'category'),
      this.period.days()._meta.load,
      this.period.dayResponsibles()._meta.load,
    ])

    this.loading = false

    this.loadEndpointData('campCollaborations', 'responsible', true)
    this.loadEndpointData('progressLabels', 'progressLabel', true)
  },
  methods: {
    async loadEndpointData(endpoint, filterKey, hasNone = false) {
      await this.camp[endpoint]()._meta.load.then(({ allItems }) => {
        const collection = allItems.map((entry) => entry._meta.self)
        if (hasNone) {
          collection.push('none')
        }
        this.filter[filterKey] =
          this.filter[filterKey].filter((value) => collection.includes(value)) ?? null
        this.loadingEndpoints[endpoint] = false
      })
    },
    showUnlockReminder(move) {
      clearTimeout(this.reminderInst)
      if (this.isOutsider) return
      this.reminderText = move
        ? this.$t('views.camp.campProgram.reminderLockedMove')
        : this.$t('views.camp.campProgram.reminderLockedCreate')
      this.showReminder = true
      this.reminderInst = setTimeout(
        () => (this.showReminder = false),
        this.REMINDER_TIMEOUT
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

<style scoped>
:root {
  --schedule-entry-filters-height: 0px;
}
</style>
