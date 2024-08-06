<template>
  <div
    v-resizeobserver:0.immediate="onResize"
    class="d-flex flex-wrap items-baseline"
    style="overflow-y: auto; gap: 10px"
  >
    <BooleanFilter
      v-if="loadingEndpoints !== true && loadingEndpoints.campCollaborations !== true"
      v-model="showOnlyMyActivities"
      :label="$tc('components.program.scheduleEntryFilters.onlyMyActivities')"
    />
    <v-skeleton-loader
      v-else
      type="button"
      class="v-skeleton-loader--inherit-size"
      height="32"
      width="160px"
    />
    <FilterDivider />
    <template v-if="!!periods">
      <template v-if="loadingEndpoints !== true && loadingEndpoints.periods !== true">
        <SelectFilter
          v-if="multiplePeriods"
          v-model="value.period"
          :items="periods"
          display-field="description"
          :label="$tc('components.program.scheduleEntryFilters.period')"
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
      v-model="value.responsible"
      multiple
      and-filter
      :items="campCollaborations"
      :display-field="campCollaborationDisplayName"
      :label="$tc('components.program.scheduleEntryFilters.responsible')"
    >
      <template #item="{ item }">
        <template v-if="item.exclusiveNone">
          {{ item.text }}
        </template>
        <template v-else>
          <TextAlignBaseline class="mr-1">
            <UserAvatar :camp-collaboration="campCollaborations[item.value]" size="20" />
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
    <SelectFilter
      v-if="loadingEndpoints !== true && loadingEndpoints.categories !== true"
      v-model="value.category"
      multiple
      :items="categories"
      display-field="short"
      :label="$tc('components.program.scheduleEntryFilters.category')"
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
    <SelectFilter
      v-if="loadingEndpoints !== true && loadingEndpoints.progressLabels !== true"
      v-model="value.progressLabel"
      multiple
      :items="progressLabels"
      display-field="title"
      :label="$tc('components.program.scheduleEntryFilters.progressLabel')"
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
import { keyBy, sortBy } from 'lodash'
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
  },
  computed: {
    multiplePeriods() {
      return this.periods && Object.keys(this.periods).length > 1
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
        },
        ...keyBy(
          sortBy(this.camp.campCollaborations().items, (u) =>
            campCollaborationDisplayName(u, this.$tc.bind(this)).toLowerCase()
          ),
          '_meta.self'
        ),
      }
    },
    categories() {
      return keyBy(this.camp.categories().items, '_meta.self')
    },
    progressLabels() {
      const labels = sortBy(this.camp.progressLabels().items, (l) => l.position)
      return {
        none: {
          title: this.$tc('components.program.scheduleEntryFilters.progressLabelNone'),
          _meta: { self: 'none' },
        },
        ...keyBy(labels, '_meta.self'),
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
          filterEquals(this.value.period, null) &&
          filterEquals(this.value.progressLabel, null)
        )
      },
      set(value) {
        this.value.responsible = value ? [this.loggedInCampCollaboration] : []
        this.value.category = []
        this.value.period = null
        this.value.progressLabel = null
      },
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
        this.value[filterKey] =
          this.value[filterKey].filter((value) => collection.includes(value)) ?? null
        this.loadingEndpoints[endpoint] = false
      })
    },
    resetFilter() {
      this.value.period = null
      this.value.category = []
      this.value.responsible = []
      this.value.progressLabel = []
    },
    onResize({ height }) {
      this.$emit('height-changed', height)
    },
  },
}
</script>

<style scoped></style>
