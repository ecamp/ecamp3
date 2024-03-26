<!--
Show all activity schedule entries of a single period.
-->

<template>
  <content-card :title="$tc('views.camp.campProgram.title')" toolbar>
    <template #title-actions>
      <period-switcher :period="period" />
      <v-spacer />
      <template v-if="$vuetify.breakpoint.mdAndUp">
        <v-toolbar-items v-if="filterSet">
          <v-chip
            label
            outlined
            :input-value="openFilter"
            color="primary"
            class="align-self-center mr-2"
            @click="openFilter = !openFilter"
          >
            <v-icon left size="20">mdi-filter</v-icon>
            1
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
            <v-icon>mdi-dots-horizontal</v-icon>
          </v-btn>
        </template>
        <v-list class="py-0">
          <LockUnlockListItem
            v-model="editMode"
            :disabled="!isContributor"
            @click="editMode = !editMode"
          />
          <v-list-item
            v-if="!$vuetify.breakpoint.mdAndUp"
            :input-value="filterSet"
            :color="filterSet ? 'primary' : null"
            @click="openFilter = !openFilter"
          >
            <v-list-item-icon>
              <v-icon>mdi-filter</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title>Filter</v-list-item-title>
            </v-list-item-content>
            <v-list-item-action v-if="filterSet">
              <v-chip label color="primary">1</v-chip>
            </v-list-item-action>
          </v-list-item>
          <v-divider />
          <DownloadNuxtPdf :config="printConfig" />
          <DownloadClientPdf :config="printConfig" />
        </v-list>
      </v-menu>
    </template>
    <template v-if="$vuetify.breakpoint.mdAndUp && openFilter" #title-extension>
      <div
        class="d-flex w-100 flex-wrap pb-4 justify-center"
        style="overflow-y: auto; gap: 10px"
      >
        <BooleanFilter
          label="Random entries"
          :value="filterSet"
          @input="filterSet = !filterSet"
        />
        <FilterDivider />
        <BooleanFilter
          :label="$tc('views.camp.dashboard.category')"
          @input="notImplemented"
        />
        <BooleanFilter
          :label="$tc('views.camp.dashboard.responsible')"
          @input="notImplemented"
        />
        <v-chip v-if="filterSet" label outlined @click="filterSet = false">
          <v-icon left>mdi-close</v-icon>
          {{ $tc('views.camp.dashboard.clearFilters') }}
        </v-chip>
      </div>
    </template>
    <ScheduleEntries :period="period" :show-button="isContributor">
      <template #default="slotProps">
        <template v-if="slotProps.loading">
          <v-skeleton-loader type="table" />
        </template>
        <template v-else>
          <Picasso
            :schedule-entries="slotProps.scheduleEntries"
            :period="period()"
            :start="period().start"
            :end="period().end"
            :editable="editMode"
            :filtered="filterSet"
            @newEntry="slotProps.on.newEntry"
            @unlockReminder="showUnlockReminder"
          />
        </template>
      </template>
    </ScheduleEntries>
    <v-snackbar v-model="showReminder" light class="mb-12">
      <v-icon>mdi-lock</v-icon>
      {{ reminderText }}
    </v-snackbar>
    <v-bottom-sheet v-if="!$vuetify.breakpoint.mdAndUp" v-model="openFilter">
      <v-sheet class="text-center" height="200px">
        <div
          class="d-flex w-100 flex-wrap pa-4 align-baseline"
          style="overflow-y: auto; gap: 10px"
        >
          Filter:
          <BooleanFilter
            label="Random entries"
            :value="filterSet"
            @input="filterSet = !filterSet"
          />
          <FilterDivider />
          <BooleanFilter
            :label="$tc('views.camp.dashboard.category')"
            @input="notImplemented"
          />
          <BooleanFilter
            :label="$tc('views.camp.dashboard.responsible')"
            @input="notImplemented"
          />
          <v-chip v-if="filterSet" label outlined @click="filterSet = false">
            <v-icon left>mdi-close</v-icon>
            {{ $tc('views.camp.dashboard.clearFilters') }}
          </v-chip>
        </div>
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
import BooleanFilter from '@/components/dashboard/BooleanFilter.vue'
import FilterDivider from '@/components/dashboard/FilterDivider.vue'

export default {
  name: 'CampProgram',
  components: {
    FilterDivider,
    BooleanFilter,
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
    period: { type: Function, required: true },
  },
  data() {
    return {
      showReminder: false,
      reminderText: null,
      openFilter: false,
      filterSet: false,
    }
  },
  computed: {
    camp() {
      return this.period().camp()
    },
    printConfig() {
      return {
        camp: this.period().camp()._meta.self,
        language: this.$store.state.lang.language,
        documentName: this.camp.title + '-picasso.pdf',
        contents: [
          {
            type: 'Picasso',
            options: {
              periods: [this.period()._meta.self],
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
  },
  methods: {
    showUnlockReminder(move) {
      this.reminderText = move
        ? this.$tc('views.camp.campProgram.reminderLockedMove')
        : this.$tc('views.camp.campProgram.reminderLockedCreate')
      this.showReminder = true
    },
    notImplemented() {
      alert('not implemented')
    },
  },
}
</script>
