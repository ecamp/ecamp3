<template>
  <div
    v-resizeobserver:0.immediate="onResize"
    class="d-flex flex-wrap items-baseline"
    style="overflow-y: auto; gap: 10px"
  >
    <template v-if="!hideSelfFilter">
      <BooleanFilter
        v-if="loadingEndpoints !== true && loadingEndpoints.campCollaborations !== true"
        v-model="showOnlyMyActivities"
        :label="$t('components.program.scheduleEntryFilters.onlyMyActivities')"
        :result-count="myActivitiesCount"
      />
      <v-skeleton-loader
        v-else
        type="button"
        class="v-skeleton-loader--inherit-size"
        height="32"
        width="160"
      />
      <FilterDivider />
    </template>
    <template v-if="!!periods && !hidePeriodFilter">
      <template v-if="loadingEndpoints !== true && loadingEndpoints.periods !== true">
        <SelectFilter
          v-if="multiplePeriods"
          v-model="modelValue.period"
          :items="periodItems"
          display-field="description"
          :label="$t('components.program.scheduleEntryFilters.period')"
          @update:model-value="(val) => updateFilter({ period: val })"
        />
      </template>
      <v-skeleton-loader
        v-else
        type="button"
        class="v-skeleton-loader--inherit-size"
        height="32"
        width="150"
      />
    </template>
    <SelectFilter
      v-if="loadingEndpoints !== true && loadingEndpoints.campCollaborations !== true"
      v-model="modelValue.responsible"
      multiple
      and-filter
      :items="campCollaborations"
      :display-field="campCollaborationDisplayName"
      :label="$t('components.program.scheduleEntryFilters.responsible')"
      @update:model-value="(val) => updateFilter({ responsible: val })"
    >
      <template #item="{ item }">
        <template v-if="item.exclusiveNone">
          <span>{{ item.text }}</span>
        </template>
        <template v-else>
          <TextAlignBaseline class="mr-1">
            <UserAvatar :camp-collaboration="campCollaborations[item.value]" size="20" />
          </TextAlignBaseline>
          <span>{{ item.text }}</span>
        </template>
      </template>
    </SelectFilter>
    <v-skeleton-loader
      v-else
      type="button"
      class="v-skeleton-loader--inherit-size"
      height="32"
      width="130"
    />
    <SelectFilter
      v-if="loadingEndpoints !== true && loadingEndpoints.categories !== true"
      v-model="modelValue.category"
      multiple
      :items="categories"
      display-field="short"
      :label="$t('components.program.scheduleEntryFilters.category')"
      @update:model-value="(val) => updateFilter({ category: val })"
    >
      <template #item="{ item }">
        <CategoryChip dense :category="categories[item.value]" class="mr-1" />
        <span>{{ categories[item.value].name }}</span>
      </template>
    </SelectFilter>
    <v-skeleton-loader
      v-else
      type="button"
      class="v-skeleton-loader--inherit-size"
      height="32"
      width="100"
    />
    <template v-if="!hideDayFilter">
      <SelectFilter
        v-if="loadingEndpoints !== true && loadingEndpoints.days !== true"
        v-model="modelValue.day"
        multiple
        :items="dayItems"
        display-field="label"
        :label="$t('components.program.scheduleEntryFilters.day')"
        @update:model-value="(val) => updateFilter({ day: val })"
      />
      <v-skeleton-loader
        v-else
        type="button"
        class="v-skeleton-loader--inherit-size"
        height="32"
        width="150"
      />
    </template>
    <SelectFilter
      v-if="loadingEndpoints !== true && loadingEndpoints.progressLabels !== true"
      v-model="modelValue.progressLabel"
      multiple
      :items="progressLabels"
      display-field="title"
      :label="$t('components.program.scheduleEntryFilters.progressLabel')"
      @update:model-value="(val) => updateFilter({ progressLabel: val })"
    >
      <template #item="{ item }">
        <span>{{ progressLabels[item.value].title }}</span>
      </template>
    </SelectFilter>
    <v-skeleton-loader
      v-else
      type="button"
      class="v-skeleton-loader--inherit-size"
      height="32"
      width="100"
    />
    <v-chip
      v-if="filterSet"
      label
      variant="flat"
      color="surface"
      border="sm"
      @click="resetFilter"
    >
      <span>&ZeroWidthSpace;</span>
      <v-icon start>mdi-close</v-icon>
      {{ $t('components.program.scheduleEntryFilters.clearFilters') }}
    </v-chip>
  </div>
</template>

<script>
import UserAvatar from '@/components/user/UserAvatar.vue'
import SelectFilter from '@/components/dashboard/SelectFilter.vue'
import CategoryChip from '@/components/generic/CategoryChip.vue'
import TextAlignBaseline from '@/components/layout/TextAlignBaseline.vue'
import BooleanFilter from '@/components/dashboard/BooleanFilter.vue'
import FilterDivider from '@/components/dashboard/FilterDivider.vue'
import { mapGetters } from 'vuex'
import { clone, keyBy, sortBy } from 'lodash-es'
import campCollaborationDisplayName from '@/common/helpers/campCollaborationDisplayName.js'

function filterEquals(arr1, arr2) {
  return JSON.stringify(arr1) === JSON.stringify(arr2)
}

export default {
  name: 'ScheduleEntryFilters',
  components: {
    FilterDivider,
    BooleanFilter,
    TextAlignBaseline,
    CategoryChip,
    SelectFilter,
    UserAvatar,
  },
  props: {
    modelValue: {
      type: Object,
      default: () => ({
        period: null,
        category: [],
        responsible: [],
        progressLabel: [],
      }),
    },
    camp: {
      type: Object,
      required: true,
    },
    periods: {
      type: Object,
      default: null,
    },
    loadingEndpoints: {
      type: [Boolean, Object],
      default: true,
    },
    filterFn: {
      type: Function,
      default: () => [],
    },
    hideSelfFilter: {
      type: Boolean,
      default: false,
    },
    hidePeriodFilter: {
      type: Boolean,
      default: false,
    },
    hideDayFilter: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['update:modelValue', 'heightChanged'],
  computed: {
    periodItems() {
      return keyBy(
        Object.values(this.periods).map((period) => ({
          ...period,
          resultCount: this.resultCountWithModifiedFilter('period', period._meta.self),
        })),
        '_meta.self'
      )
    },
    multiplePeriods() {
      return this.periods && Object.keys(this.periods).length > 1
    },
    dayItems() {
      return keyBy(
        this.camp.periods().items.flatMap((period) =>
          period.days().items.map((day) => ({
            ...day,
            label: this.$t(
              'components.program.scheduleEntryFilters.dayLabel',
              {
                dayNumber: day.number,
                date: this.$date.utc(day.start).format('dd. DD. MMM'),
              },
              0
            ),
            resultCount: this.resultCountWithModifiedFilter('day', day._meta.self),
          }))
        ),
        '_meta.self'
      )
    },
    ...mapGetters({
      loggedInUser: 'getLoggedInUser',
    }),
    loggedInCampCollaboration() {
      return Object.values(this.campCollaborations).find((collaboration) => {
        if (typeof collaboration.user !== 'function' || !this.loggedInUser) {
          return false
        }
        return this.loggedInUser?._meta?.self === collaboration.user?.()?._meta?.self
      })?._meta?.self
    },
    campCollaborations() {
      return {
        none: {
          exclusiveNone: true,
          label: this.$t('components.program.scheduleEntryFilters.responsibleNone'),
          _meta: { self: 'none' },
          resultCount: this.resultCountWithModifiedFilter('responsible', ['none']),
        },
        ...keyBy(
          sortBy(this.camp.campCollaborations().items, (u) =>
            campCollaborationDisplayName(u, this.$t.bind(this)).toLowerCase()
          ).map((campCollaboration) => {
            return {
              ...campCollaboration,
              resultCount: this.resultCountWithModifiedFilter(
                'responsible',
                this.modelValue.responsible?.includes('none')
                  ? [campCollaboration._meta.self]
                  : [...(this.modelValue.responsible ?? []), campCollaboration._meta.self]
              ),
            }
          }),
          '_meta.self'
        ),
      }
    },
    categories() {
      return keyBy(
        this.camp.categories().items.map((category) => {
          return {
            ...category,
            resultCount: this.resultCountWithModifiedFilter('category', [
              category._meta.self,
            ]),
          }
        }),
        '_meta.self'
      )
    },
    progressLabels() {
      const labels = sortBy(this.camp.progressLabels().items, (l) => l.position)
      return {
        none: {
          title: this.$t('components.program.scheduleEntryFilters.progressLabelNone'),
          _meta: { self: 'none' },
          resultCount: this.resultCountWithModifiedFilter('progressLabel', ['none']),
        },
        ...keyBy(
          labels.map((label) => {
            return {
              ...label,
              resultCount: this.resultCountWithModifiedFilter('progressLabel', [
                label._meta.self,
              ]),
            }
          }),
          '_meta.self'
        ),
      }
    },
    filteredPropertiesCount() {
      return Object.entries(this.modelValue).filter(
        ([key, item]) =>
          key !== 'activityCount' && (Array.isArray(item) ? item.length : !!item)
      ).length
    },
    filterSet() {
      return this.filteredPropertiesCount > 0
    },
    showOnlyMyActivities: {
      get() {
        return (
          filterEquals(this.modelValue.responsible, [this.loggedInCampCollaboration]) &&
          filterEquals(this.modelValue.category, []) &&
          filterEquals(this.modelValue.day, []) &&
          filterEquals(this.modelValue.period, null) &&
          filterEquals(this.modelValue.progressLabel, [])
        )
      },
      set(value) {
        this.updateFilter({
          responsible: value ? [this.loggedInCampCollaboration] : [],
          category: [],
          day: [],
          period: null,
          progressLabel: [],
        })
      },
    },
    myActivitiesCount() {
      return this.filterFn({
        responsible: [this.loggedInCampCollaboration],
        category: [],
        period: null,
        progressLabel: null,
      }).length
    },
  },
  methods: {
    campCollaborationDisplayName(campCollaboration) {
      return campCollaborationDisplayName(campCollaboration, this.$t.bind(this))
    },
    resetFilter() {
      this.updateFilter({
        period: null,
        day: [],
        category: [],
        responsible: [],
        progressLabel: [],
      })
    },
    onResize({ height }) {
      this.$emit('heightChanged', height)
    },
    resultCountWithModifiedFilter(filterName, filterValue) {
      return this.filterFn({
        ...this.modelValue,
        [filterName]: filterValue,
      }).length
    },
    updateFilter(updates = {}) {
      const valueClone = clone(this.modelValue)
      Object.assign(valueClone, updates)
      this.$emit('update:modelValue', valueClone)
    },
  },
}
</script>

<style scoped></style>
