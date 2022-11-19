<template>
  <content-card :title="$tc('views.camp.dashboard.activities')" toolbar>
    <div class="d-flow-root">
      <div class="d-flex flex-wrap ma-4" style="overflow-y: auto; gap: 10px">
        <BooleanFilter
          v-if="!loading"
          v-model="showOnlyMyActivities"
          :label="$tc('views.camp.dashboard.onlyMyActivities')"
        />
        <v-skeleton-loader v-else type="button" />
        <FilterDivider />
        <template v-if="!loading">
          <SelectFilter
            v-model="filter.responsible"
            multiple
            and-filter
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
            v-if="multiplePeriods"
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
        </template>
        <template v-else>
          <v-skeleton-loader type="button" />
          <v-skeleton-loader type="button" />
        </template>
      </div>
      <template v-if="!loading && !scheduleEntriesLoading">
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
          <template v-if="!periods[uri].days()._meta.loading">
            <tbody
              v-for="(dayScheduleEntries, dayUri) in periodDays"
              :key="dayUri"
              :aria-labelledby="dayUri + 'th'"
            >
              <tr>
                <th :id="dayUri + 'th'" colspan="5" scope="colgroup" class="day-header">
                  {{ dateLong(days[dayUri].start) }}
                </th>
              </tr>
              <ActivityRow
                v-for="scheduleEntry in dayScheduleEntries"
                :key="scheduleEntry._meta.self"
                :schedule-entry="scheduleEntry"
              />
            </tbody>
          </template>
        </table>
        <p
          v-if="scheduleEntries.length > 0 && filteredScheduleEntries.length === 0"
          class="ma-4"
        >
          {{ $tc('views.camp.dashboard.noEntries') }}
        </p>
        <p v-if="scheduleEntries.length === 0" class="ma-4">
          {{ $tc('views.camp.dashboard.welcome') }}
        </p>
      </template>
      <table v-else class="mx-4 mt-6 mb-3 d-sr-none" style="border-collapse: collapse">
        <caption>
          <v-skeleton-loader type="heading" class="d-block mb-3" width="45ch" />
        </caption>
        <tbody>
          <tr>
            <th colspan="5" class="day-header">
              <v-skeleton-loader type="text" width="15ch" />
            </th>
          </tr>
          <tr
            v-for="index in 3"
            :key="index"
            style="border-top: 1px solid #ddd; vertical-align: top"
          >
            <th class="pt-1">
              <v-skeleton-loader type="text" width="2ch" />
              <v-skeleton-loader type="text" width="3ch" class="d-sm-none" />
            </th>
            <td class="d-none d-sm-table-cell pl-2 pt-1">
              <v-skeleton-loader type="text" width="3ch" />
            </td>
            <td class="nowrap pl-2 pt-1">
              <v-skeleton-loader type="text" width="6ch" />
              <v-skeleton-loader type="text" width="4ch" />
            </td>
            <td style="width: 100%" class="pl-2 pb-2 pt-1">
              <v-skeleton-loader type="text" width="20ch" />
              <v-skeleton-loader type="text" width="15ch" />
            </td>
            <td class="contentrow avatarrow overflow-visible pt-1">
              <v-skeleton-loader type="heading" width="55" />
            </td>
          </tr>
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
import { dateLong } from '../../common/helpers/dateHelperUTCFormatted.js'

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
    scheduleEntriesLoading() {
      return Object.values(this.periods).some(
        (period) => period.scheduleEntries()._meta.loading
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
      return Object.values(this.campCollaborations).find((collaboration) => {
        if (typeof collaboration.user !== 'function') {
          return false
        }
        return collaboration.user()?._meta?.self === this.loggedInUser._meta.self
      })?._meta?.self
    },
    multiplePeriods() {
      return Object.keys(this.periods).length > 1
    },
  },
  async mounted() {
    this.api.reload(this.camp())

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
    campCollaborationDisplayName(campCollaboration) {
      return campCollaborationDisplayName(campCollaboration, this.$tc.bind(this))
    },
    dateLong,
  },
}
</script>

<style scoped>
.day-header {
  padding-top: 0.75rem;
  font-weight: 400;
  color: #666;
  font-size: 0.9rem;
  text-align: left;
}
</style>
