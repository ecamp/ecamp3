<template>
  <DetailPane
    v-model="dialogOpen"
    max-width="900px"
    :title="$t('components.print.config.dialogScheduleEntryFilter.title')"
    icon="mdi-filter"
    :cancel-action="close"
    :cancel-visible="false"
    @update:model-value="emit"
  >
    <template #activator="{ props }">
      <v-chip
        label
        variant="outlined"
        :color="anyFilter ? 'primary' : 'secondary'"
        border="sm"
        class="align-self-stretch mt-4 mb-4"
        :class="anyFilter && 'border-current'"
        v-bind="props"
      >
        <v-icon start size="20">mdi-filter</v-icon>
        <v-skeleton-loader
          v-if="filterDataLoading"
          type="text"
          width="100%"
          class="v-skeleton-loader--no-margin"
        />
        <span v-else class="flex-grow-1 text-center">{{ activatorLabel }}</span>
      </v-chip>
    </template>
    <ScheduleEntryFilters
      v-model="localFilter"
      :camp="camp"
      :filter-fn="filterFn"
      :loading-endpoints="loadingEndpoints"
      :hide-self-filter="isOutsider"
      :hide-period-filter="hidePeriodFilter"
      :hide-day-filter="hideDayFilter"
      @update:model-value="updateLocalFilter"
    />
    <template #moreActions>
      <v-skeleton-loader
        v-if="filterDataLoading"
        type="text"
        width="20ch"
        class="v-skeleton-loader--no-margin"
      />
      <template v-else>
        {{ resultCountLabel }}
      </template>
    </template>
  </DetailPane>
</template>
<script>
import ScheduleEntryFilters from '../../program/ScheduleEntryFilters.vue'
import DetailPane from '../../generic/DetailPane.vue'
import { campRoleMixin } from '../../../mixins/campRoleMixin.js'

export default {
  name: 'DialogScheduleEntryFilter',
  components: { DetailPane, ScheduleEntryFilters },
  mixins: [campRoleMixin],
  inject: ['loadingEndpoints'],
  props: {
    camp: { type: Object, required: true },
    filterFn: { type: Function, required: true },
    modelValue: { type: Object, required: true },
    hidePeriodFilter: { type: Boolean, default: false },
    hideDayFilter: { type: Boolean, default: false },
  },
  emits: ['update:modelValue'],
  data() {
    return {
      dialogOpen: false,
      localFilter: this.modelValue,
    }
  },
  computed: {
    filterDataLoading() {
      return Object.values(this.loadingEndpoints).some(Boolean)
    },
    filteredCount() {
      if (this.filterDataLoading) return 0
      return this.filterFn(this.localFilter).length
    },
    totalCount() {
      return this.filterFn({}).length
    },
    activatorLabel() {
      if (this.anyFilter)
        return this.$t('components.print.config.dialogScheduleEntryFilter.filterActive', {
          filtered: this.filteredCount,
          total: this.totalCount,
        })
      return this.$t(
        'components.print.config.dialogScheduleEntryFilter.filterActivities',
        {
          total: this.totalCount,
        }
      )
    },
    resultCountLabel() {
      return this.$t(
        'components.print.config.dialogScheduleEntryFilter.resultCount',
        { filtered: this.filteredCount, total: this.totalCount },
        1
      )
    },
    anyFilter() {
      return (
        this.localFilter.period ||
        (this.localFilter.day != null && this.localFilter.day.length > 0) ||
        (this.localFilter.responsible != null &&
          this.localFilter.responsible.length > 0) ||
        (this.localFilter.category != null && this.localFilter.category.length > 0) ||
        (this.localFilter.progressLabel != null &&
          this.localFilter.progressLabel.length > 0)
      )
    },
  },
  watch: {
    modelValue(value) {
      this.localFilter = value
    },
    filteredCount: {
      handler(val) {
        if (this.filterDataLoading) return
        this.localFilter.activityCount = val
        if (!this.dialogOpen) {
          this.$emit('update:modelValue', this.localFilter)
        }
      },
      immediate: true,
    },
    filterDataLoading(loading) {
      if (loading) return
      this.localFilter.activityCount = this.filteredCount
      if (!this.dialogOpen) {
        this.$emit('update:modelValue', this.localFilter)
      }
    },
  },
  methods: {
    updateLocalFilter(filter) {
      this.localFilter = filter
      if (!this.filterDataLoading) {
        this.localFilter.activityCount = this.filteredCount
      }
    },
    emit(dialogOpen) {
      if (dialogOpen) return // only emit when closing dialog
      this.$emit('update:modelValue', this.localFilter)
    },
    close() {
      this.dialogOpen = false
      this.emit()
    },
  },
}
</script>
<style scoped>
:deep(.v-chip__content) {
  width: 100%;
}
</style>
