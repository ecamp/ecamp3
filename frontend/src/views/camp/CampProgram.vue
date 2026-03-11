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
        <template v-if="isFilterSet">
          <v-toolbar-items>
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
        </template>
        <v-menu offset-y :close-on-content-click="false">
          <template #activator="{ on, attrs }">
            <v-chip
              outlined
              label
              class="mr-1"
              :input-value="showJsCheck"
              v-bind="attrs"
              v-on="on"
            >
              <v-icon size="20" :color="showJsCheck ? 'primary' : 'rgba(0, 0, 0, 0.54)'">
                mdi-flask-outline
              </v-icon>
            </v-chip>
          </template>
          <v-sheet width="360" class="pa-4">
            <div class="text-subtitle-2 mb-2">
              {{ $tc('views.camp.campProgram.jsCheck.title') }}
            </div>
            <v-alert
              dense
              outlined
              type="info"
              class="mb-3"
            >
              {{ $tc('views.camp.campProgram.jsCheck.experimental') }}
            </v-alert>
            <div class="text-body-2 mb-3">
              {{ $tc('views.camp.campProgram.jsCheck.description') }}
            </div>
            <div class="text-body-2 mb-3">
              {{ $tc('views.camp.campProgram.jsCheck.rules') }}
            </div>
            <div class="text-caption text--secondary mb-2">
              {{ $tc('views.camp.campProgram.jsCheck.daytimesDescription') }}
            </div>
            <div
              v-for="daytime in daytimeFields"
              :key="daytime.key"
              class="d-flex align-center mb-2"
            >
              <div class="e-camp-program__daytime-label text-body-2 mr-3">
                {{ $tc(daytime.label) }}
              </div>
              <v-text-field
                v-model="jsCheckDaytimes[daytime.key].start"
                type="time"
                dense
                outlined
                hide-details
                class="mr-2"
                :label="$tc('views.camp.campProgram.jsCheck.start')"
              />
              <v-text-field
                v-model="jsCheckDaytimes[daytime.key].end"
                type="time"
                dense
                outlined
                hide-details
                :label="$tc('views.camp.campProgram.jsCheck.end')"
              />
            </div>
            <div class="text-caption text--secondary mb-2">
              {{ $tc('views.camp.campProgram.jsCheck.categoryPrefixesDescription') }}
            </div>
            <div class="d-flex align-center mb-2">
              <div class="e-camp-program__daytime-label text-body-2 mr-3" style="min-width: 60px">
                {{ $tc('views.camp.campProgram.jsCheck.lsPrefix') }}
              </div>
              <v-text-field
                v-model="jsCheckCategoryPrefixes.ls"
                dense
                outlined
                hide-details
                :placeholder="$tc('views.camp.campProgram.jsCheck.lsPrefixPlaceholder')"
              />
            </div>
            <div class="d-flex align-center mb-3">
              <div class="e-camp-program__daytime-label text-body-2 mr-3" style="min-width: 60px">
                {{ $tc('views.camp.campProgram.jsCheck.laPrefix') }}
              </div>
              <v-text-field
                v-model="jsCheckCategoryPrefixes.la"
                dense
                outlined
                hide-details
                :placeholder="$tc('views.camp.campProgram.jsCheck.laPrefixPlaceholder')"
              />
            </div>
            <v-switch
              v-model="showJsCheck"
              inset
              hide-details
              :label="$tc('views.camp.campProgram.jsCheck.toggle')"
            />
          </v-sheet>
        </v-menu>
        <v-chip
          v-if="!isFilterSet"
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
        v-if="!isOutsider"
        v-model="editMode"
        :shake="showReminder"
        :disabled-for-guest="!isContributor"
        @click="editMode = !editMode"
      />
      <v-menu offset-y>
        <template #activator="{ on, attrs }">
          <v-btn icon v-bind="attrs" data-testid="campprogram-menu" v-on="on">
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
            v-if="!isOutsider"
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
          <v-list-item>
            <v-list-item-icon>
              <v-icon :color="showJsCheck ? 'primary' : null">
                mdi-flask-outline
              </v-icon>
            </v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title>
                {{ $tc('views.camp.campProgram.jsCheck.title') }}
              </v-list-item-title>
              <v-list-item-subtitle>
                {{ $tc('views.camp.campProgram.jsCheck.experimental') }}
              </v-list-item-subtitle>
            </v-list-item-content>
            <v-list-item-action>
              <v-switch
                v-model="showJsCheck"
                inset
                hide-details
                @click.stop
              />
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
      :filter-fn="filterFn"
      :hide-self-filter="isOutsider"
      hide-period-filter
      hide-day-filter
      @height-changed="scheduleEntryFiltersHeightChanged"
    />
    <template v-if="loading">
      <v-skeleton-loader type="table" />
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
          :show-js-compliance="showJsCheck"
          :js-compliance-daytimes="jsCheckDaytimes"
          :js-compliance-category-prefixes="jsCheckCategoryPrefixes"
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
import { DEFAULT_JS_COMPLIANCE_DAYTIMES, DEFAULT_JS_COMPLIANCE_CATEGORY_PREFIXES } from '@/components/program/picasso/jsCoachCheck.js'

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
        days: false,
        campCollaborations: true,
        progressLabels: true,
      },
      showJsCheck: false,
      jsCheckDaytimes: JSON.parse(
        JSON.stringify(DEFAULT_JS_COMPLIANCE_DAYTIMES)
      ),
      jsCheckCategoryPrefixes: JSON.parse(
        JSON.stringify(DEFAULT_JS_COMPLIANCE_CATEGORY_PREFIXES)
      ),
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
        this.$tc('views.camp.campProgram.title') + ': ' + this.period.description,
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
    daytimeFields() {
      return [
        {
          key: 'morning',
          label: 'views.camp.campProgram.jsCheck.daytimes.morning',
        },
        {
          key: 'afternoon',
          label: 'views.camp.campProgram.jsCheck.daytimes.afternoon',
        },
        {
          key: 'evening',
          label: 'views.camp.campProgram.jsCheck.daytimes.evening',
        },
      ]
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
      if (this.isOutsider) return
      this.reminderText = move
        ? this.$tc('views.camp.campProgram.reminderLockedMove')
        : this.$tc('views.camp.campProgram.reminderLockedCreate')
      this.showReminder = true
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

.e-camp-program__daytime-label {
  min-width: 5.5rem;
}
</style>
