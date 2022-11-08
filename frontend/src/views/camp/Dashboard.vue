<template>
  <content-card :title="$tc('views.camp.dashboard.activities')" toolbar>
    <div class="d-flow-root">
      <div
        v-if="!loading"
        class="d-flex flex-wrap ma-4"
        style="overflow-y: auto; gap: 10px"
      >
        <BooleanFilter
          v-model="showOnlyMyActivities"
          :label="$tc('views.camp.dashboard.onlyMyActivities')"
        />
        <FilterDivider />
        <SelectFilter
          v-model="filter.responsible"
          multiple
          :items="campCollaborations"
          :display-field="campCollaborationDisplayName"
          :label="$tc('views.camp.dashboard.responsible')"
        >
          <template #item="{ item }">
            <UserAvatar
              :camp-collaboration="campCollaborations[item.value]"
              size="18"
              class="mr-1"
            />
            {{ item.text }}
          </template>
        </SelectFilter>
        <SelectFilter
          v-model="filter.category"
          multiple
          :items="categories"
          display-field="short"
          :label="$tc('views.camp.dashboard.category')"
        >
          <template #item="{ item }">
            <CategoryChip dense :category="categories[item.value]" class="mr-1" />
            {{ categories[item.value].name }}
          </template>
        </SelectFilter>
        <SelectFilter
          v-if="groupedScheduleEntries.length > 1"
          v-model="filter.period"
          :items="periods"
          display-field="description"
          :label="$tc('views.camp.dashboard.period')"
        />
        <v-chip
          v-if="
            filter.period ||
            (filter.responsible && filter.responsible.length > 0) ||
            (filter.category && filter.category.length > 0)
          "
          label
          outlined
          @click="
            filter = {
              period: null,
              responsible: [],
              category: [],
            }
          "
        >
          <v-icon left>mdi-close</v-icon>
          {{ $tc('views.camp.dashboard.clearFilters') }}
        </v-chip>
      </div>
      <table
        v-for="(periodDays, uri) in groupedScheduleEntries"
        :key="uri"
        class="mx-4 mt-6 mb-3"
        style="border-collapse: collapse"
      >
        <caption class="font-weight-bold text-left">
          {{
            periods[uri].description
          }}
        </caption>
        <thead :key="uri + '_head'">
          <tr class="d-sr-only">
            <th :id="uri + 'th-number'" scope="col">
              {{ $tc('views.camp.dashboard.columns.number') }}
            </th>
            <th :id="uri + 'th-category'" scope="col">
              {{ $tc('views.camp.dashboard.columns.category') }}
            </th>
            <th :id="uri + 'th-time'" scope="col">
              {{ $tc('views.camp.dashboard.columns.time') }}
            </th>
            <th :id="uri + 'th-title'" scope="col">
              {{ $tc('views.camp.dashboard.columns.title') }}
            </th>
            <th :id="uri + 'th-responsible'" scope="col">
              {{ $tc('views.camp.dashboard.columns.responsible') }}
            </th>
          </tr>
        </thead>
        <tbody
          v-for="(dayScheduleEntries, dayUri) in periodDays"
          :key="dayUri"
          :aria-labelledby="dayUri + 'th'"
        >
          <tr>
            <th
              :id="dayUri + 'th'"
              colspan="5"
              scope="colgroup"
              style="
                padding-top: 0.75rem;
                font-weight: 400;
                color: #666;
                font-size: 0.9rem;
                text-align: left;
              "
            >
              {{ days[dayUri].date }}
            </th>
          </tr>
          <ActivityRow
            v-for="scheduleEntry in dayScheduleEntries"
            :key="scheduleEntry._meta.self"
            :schedule-entry="scheduleEntry"
          />
        </tbody>
      </table>
    </div>
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import UserAvatar from '../../components/user/UserAvatar.vue'
import CategoryChip from '@/components/generic/CategoryChip.vue'
import BooleanFilter from '@/components/dashboard/BooleanFilter.vue'
import SelectFilter from '@/components/dashboard/SelectFilter.vue'
import ActivityRow from '@/components/dashboard/ActivityRow.vue'
import FilterDivider from '@/components/dashboard/FilterDivider.vue'
import { keyBy, groupBy, mapValues } from 'lodash'
import campCollaborationDisplayName from '../../common/helpers/campCollaborationDisplayName.js'

function filterEquals(arr1, arr2) {
  return JSON.stringify(arr1) === JSON.stringify(arr2)
}

export default {
  name: 'Dashboard',
  components: {
    FilterDivider,
    ActivityRow,
    SelectFilter,
    BooleanFilter,
    CategoryChip,
    ContentCard,
    UserAvatar,
  },
  props: {
    camp: { type: Function, required: true },
  },
  data() {
    return {
      loggedInUser: null,
      loading: true,
      filter: {
        period: null,
        responsible: [],
        category: [],
      },
    }
  },
  computed: {
    campCollaborations() {
      return keyBy(this.camp().campCollaborations().items, '_meta.self')
    },
    categories() {
      return keyBy(this.camp().categories().items, '_meta.self')
    },
    periods() {
      return keyBy(this.camp().periods().items, '_meta.self')
    },
    scheduleEntries() {
      return Object.values(this.periods).flatMap(
        (period) => period.scheduleEntries().items
      )
    },
    days() {
      return keyBy(
        Object.values(this.periods).flatMap((period) => period.days().items),
        '_meta.self'
      )
    },
    filteredScheduleEntries() {
      return this.scheduleEntries.filter(
        (scheduleEntry) =>
          // filter by period
          (this.filter.period === null ||
            scheduleEntry.period()._meta.self === this.filter.period) &&
          // filter by categories: OR filter
          (this.filter.category === null ||
            this.filter.category.length === 0 ||
            this.filter.category?.includes(
              scheduleEntry.activity().category()._meta.self
            )) &&
          // filter by responsibles: AND filter
          (this.filter.responsible === null ||
            this.filter.responsible.length === 0 ||
            this.filter.responsible?.every((responsible) => {
              return scheduleEntry
                .activity()
                .activityResponsibles()
                .items.map((responsible) => responsible.campCollaboration()._meta.self)
                .includes(responsible)
            }))
      )
    },
    groupedScheduleEntries() {
      const groupedByPeriod = groupBy(
        this.filteredScheduleEntries,
        (scheduleEntry) => scheduleEntry.period()._meta.self
      )
      return mapValues(groupedByPeriod, (scheduleEntries) =>
        groupBy(scheduleEntries, (scheduleEntry) => {
          return scheduleEntry.day()._meta.self
        })
      )
    },
    showOnlyMyActivities: {
      get() {
        return (
          filterEquals(this.filter.responsible, [this.loggedInCampCollaboration]) &&
          filterEquals(this.filter.category, []) &&
          filterEquals(this.filter.period, null)
        )
      },
      set(value) {
        this.filter.responsible = value ? [this.loggedInCampCollaboration] : []
        this.filter.category = []
        this.filter.period = null
      },
    },
    loggedInCampCollaboration() {
      return Object.values(this.campCollaborations).find(
        (collaboration) =>
          collaboration.user()._meta.self === this.loggedInUser._meta.self
      )?._meta?.self
    },
  },
  async mounted() {
    const [loggedInUser] = await Promise.all([
      this.$auth.loadUser(),
      this.camp().periods()._meta.load,
      this.camp().activities()._meta.load,
      this.camp().categories()._meta.load,
    ])

    this.loggedInUser = loggedInUser
    this.loading = false
  },
  methods: {
    campCollaborationDisplayName,
  },
}
</script>

<style scoped></style>
