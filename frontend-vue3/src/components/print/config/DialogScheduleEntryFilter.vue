<template>
  <DetailPane
    v-model="dialogOpen"
    max-width="900px"
    :title="$t('components.print.config.dialogScheduleEntryFilter.title')"
    icon="mdi-filter"
    :cancel-action="close"
    :cancel-visible="false"
    @input="emit"
  >
    <template #activator="{ on, attrs }">
      <v-chip
        :input-value="dialogOpen"
        label
        outlined
        :color="anyFilter ? 'primary' : null"
        class="align-self-stretch mt-4 mb-4"
        v-bind="attrs"
        v-on="on"
      >
        <v-icon left size="20">mdi-filter</v-icon>
        <span class="flex-grow-1 text-center">{{ activatorLabel }}</span>
      </v-chip>
    </template>
    <ScheduleEntryFilters
      v-model="localFilter"
      :camp="camp"
      :filter-fn="filterFn"
      :loading-endpoints="{}"
      :hide-period-filter="hidePeriodFilter"
      :hide-day-filter="hideDayFilter"
    />
    <template #moreActions>
      {{ resultCountLabel }}
    </template>
  </DetailPane>
</template>
<script>
import ScheduleEntryFilters from '../../program/ScheduleEntryFilters.vue'
import DetailPane from '../../generic/DetailPane.vue'

export default {
  name: 'DialogScheduleEntryFilter',
  components: { DetailPane, ScheduleEntryFilters },
  props: {
    camp: { type: Object, required: true },
    filterFn: { type: Function, required: true },
    filter: { type: Object, required: true },
    hidePeriodFilter: { type: Boolean, default: false },
    hideDayFilter: { type: Boolean, default: false },
  },
  data() {
    return {
      dialogOpen: false,
      localFilter: this.filter,
    }
  },
  computed: {
    activatorLabel() {
      if (this.anyFilter)
        return this.$t(
          'components.print.config.dialogScheduleEntryFilter.filterActive',
          1,
          {
            filtered: this.filterFn(this.localFilter).length,
            total: this.filterFn({}).length,
          }
        )
      return this.$t(
        'components.print.config.dialogScheduleEntryFilter.filterActivities',
        1,
        {
          total: this.filterFn({}).length,
        }
      )
    },
    resultCountLabel() {
      return this.$t(
        'components.print.config.dialogScheduleEntryFilter.resultCount',
        1,
        {
          filtered: this.filterFn(this.localFilter).length,
          total: this.filterFn({}).length,
        }
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
  methods: {
    emit(dialogOpen) {
      if (dialogOpen) return // only emit when closing dialog
      this.$emit('input', this.localFilter)
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
