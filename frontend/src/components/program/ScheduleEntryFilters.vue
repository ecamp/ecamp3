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
        :label="$tc('components.program.scheduleEntryFilters.onlyMyActivities')"
        :result-count="myActivitiesCount"
      />
      <v-skeleton-loader
        v-else
        type="button"
        class="v-skeleton-loader--inherit-size"
        height="32"
        width="160px"
      />
      <FilterDivider />
    </template>
    <template v-if="!!periods && !hidePeriodFilter">
      <template v-if="loadingEndpoints !== true && loadingEndpoints.periods !== true">
        <SelectFilter
          v-if="multiplePeriods"
          v-model="value.period"
          :items="periodItems"
          display-field="description"
          :label="$tc('components.program.scheduleEntryFilters.period')"
          @input="(val) => updateFilter({ period: val })"
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
    <template v-if="!hideCollaboratorFilter">
      <SelectFilter
        v-if="loadingEndpoints !== true && loadingEndpoints.campCollaborations !== true"
        v-model="value.responsible"
        multiple
        and-filter
        :items="campCollaborations"
        :display-field="campCollaborationDisplayName"
        :label="$tc('components.program.scheduleEntryFilters.responsible')"
        @input="(val) => updateFilter({ responsible: val })"
      >
        <template #item="{ item }">
          <template v-if="item.exclusiveNone">
            {{ item.text }}
          </template>
          <template v-else>
            <TextAlignBaseline class="mr-1">
              <UserAvatar
                :camp-collaboration="campCollaborations[item.value]"
                size="20"
              />
            </TextAlignBaseline>
            {{ item.text }}
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
    </template>
    <SelectFilter
      v-if="loadingEndpoints !== true && loadingEndpoints.categories !== true"
      v-model="value.category"
      multiple
      :items="categories"
      display-field="short"
      :label="$tc('components.program.scheduleEntryFilters.category')"
      @input="(val) => updateFilter({ category: val })"
    >
      <template #item="{ item }">
        <CategoryChip dense :category="categories[item.value]" class="mr-1" />
        {{ categories[item.value].name }}
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
        v-model="value.day"
        multiple
        :items="dayItems"
        display-field="label"
        :label="$tc('components.program.scheduleEntryFilters.day')"
        @input="(val) => updateFilter({ day: val })"
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
      v-model="value.progressLabel"
      multiple
      :items="progressLabels"
      display-field="title"
      :label="$tc('components.program.scheduleEntryFilters.progressLabel')"
      @input="(val) => updateFilter({ progressLabel: val })"
    >
      <template #item="{ item }">
        {{ progressLabels[item.value].title }}
      </template>
    </SelectFilter>
    <v-skeleton-loader
      v-else
      type="button"
      class="v-skeleton-loader--inherit-size"
      height="32"
      width="100"
    />
    <v-chip v-if="filterSet" label outlined @click="resetFilter">
      <span>&ZeroWidthSpace;</span>
      <v-icon left>mdi-close</v-icon>
      {{ $tc('components.program.scheduleEntryFilters.clearFilters') }}
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
    value: {
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
    hideCollaboratorFilter: {
      type: Boolean,
      default: false,
    },
    hideDayFilter: {
      type: Boolean,
      default: false,
    },
  },
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
            label: this.$tc('components.program.scheduleEntryFilters.dayLabel', 0, {
              dayNumber: day.number,
              date: this.$date.utc(day.start).format('dd. DD. MMM'),
            }),
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
        if (typeof collaboration.user !== 'function') {
          return false
        }
        return this.loggedInUser?._meta?.self === collaboration.user?.()?._meta?.self
      })?._meta?.self
    },
    campCollaborations() {
      return {
        none: {
          exclusiveNone: true,
          label: this.$tc('components.program.scheduleEntryFilters.responsibleNone'),
          _meta: { self: 'none' },
          resultCount: this.resultCountWithModifiedFilter('responsible', ['none']),
        },
        ...keyBy(
          sortBy(this.camp.campCollaborations().items, (u) =>
            campCollaborationDisplayName(u, this.$tc.bind(this)).toLowerCase()
          ).map((campCollaboration) => {
            return {
              ...campCollaboration,
              resultCount: this.resultCountWithModifiedFilter(
                'responsible',
                this.value.responsible?.includes('none')
                  ? [campCollaboration._meta.self]
                  : [...(this.value.responsible ?? []), campCollaboration._meta.self]
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
          title: this.$tc('components.program.scheduleEntryFilters.progressLabelNone'),
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
      return Object.values(this.value).filter((item) =>
        Array.isArray(item) ? item.length : !!item
      ).length
    },
    filterSet() {
      return this.filteredPropertiesCount > 0
    },
    showOnlyMyActivities: {
      get() {
        return (
          filterEquals(this.value.responsible, [this.loggedInCampCollaboration]) &&
          filterEquals(this.value.category, []) &&
          filterEquals(this.value.day, []) &&
          filterEquals(this.value.period, null) &&
          filterEquals(this.value.progressLabel, [])
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
  mounted() {
    if (this.loadingEndpoints !== true) {
      this.loadEndpointData('categories', 'category')
      this.loadEndpointData('campCollaborations', 'responsible', true)
      this.loadEndpointData('progressLabels', 'progressLabel', true)
    }
  },
  methods: {
    campCollaborationDisplayName(campCollaboration) {
      return campCollaborationDisplayName(campCollaboration, this.$tc.bind(this))
    },
    loadEndpointData(endpoint, filterKey, hasNone = false) {
      this.camp[endpoint]()._meta.load.then(({ allItems }) => {
        const collection = allItems.map((entry) => entry._meta.self)
        if (hasNone) {
          collection.push('none')
        }
        this.updateFilter({
          [filterKey]:
            this.value[filterKey].filter((value) => collection.includes(value)) ?? null,
        })
        this.loadingEndpoints[endpoint] = false
      })
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
      this.$emit('height-changed', height)
    },
    resultCountWithModifiedFilter(filterName, filterValue) {
      return this.filterFn({
        ...this.value,
        [filterName]: filterValue,
      }).length
    },
    updateFilter(updates = {}) {
      const valueClone = clone(this.value)
      Object.assign(valueClone, updates)
      this.$emit('input', valueClone)
    },
  },
}
</script>

<style scoped></style>
