<template>
  <content-card :title="$tc('views.camp.dashboard.activities')" toolbar>
    <div class="d-flow-root">
      <div class="d-flex flex-wrap ma-4" style="overflow-y: auto; gap: 10px">
        <BooleanFilter
          v-if="!loadingEndpoints.campCollaborations"
          v-model="showOnlyMyActivities"
          :label="$tc('views.camp.dashboard.onlyMyActivities')"
        />
        <v-skeleton-loader
          v-else
          type="button"
          class="v-skeleton-loader--inherit-size"
          height="32"
          width="160px"
        />
        <FilterDivider />
        <template v-if="!loadingEndpoints.periods">
          <SelectFilter
            v-if="multiplePeriods"
            v-model="filter.period"
            :items="periods"
            display-field="description"
            :label="$tc('views.camp.dashboard.period')"
          />
        </template>
        <v-skeleton-loader
          v-else
          type="button"
          class="v-skeleton-loader--inherit-size"
          height="32"
          width="150"
        />
        <SelectFilter
          v-if="!loadingEndpoints.campCollaborations"
          v-model="filter.responsible"
          multiple
          and-filter
          :items="campCollaborations"
          :display-field="campCollaborationDisplayName"
          :label="$tc('views.camp.dashboard.responsible')"
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
        <SelectFilter
          v-if="!loadingEndpoints.categories"
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
        <v-skeleton-loader
          v-else
          type="button"
          class="v-skeleton-loader--inherit-size"
          height="32"
          width="100"
        />
        <SelectFilter
          v-if="!loadingEndpoints.progressLabels"
          v-model="filter.progressLabel"
          multiple
          :items="progressLabels"
          display-field="title"
          :label="$tc('views.camp.dashboard.progressLabel')"
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
        <v-chip
          v-if="
            filter.period ||
            (filter.responsible && filter.responsible.length > 0) ||
            (filter.category && filter.category.length > 0) ||
            (filter.progressLabel && filter.progressLabel.length > 0)
          "
          label
          outlined
          @click="
            filter = {
              period: null,
              responsible: [],
              category: [],
              progressLabel: [],
            }
          "
        >
          <v-icon left>mdi-close</v-icon>
          {{ $tc('views.camp.dashboard.clearFilters') }}
        </v-chip>
      </div>
      <template v-if="!loading">
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
              <tr class="day-header__row">
                <th :id="dayUri + 'th'" colspan="5" scope="colgroup" class="day-header">
                  <div class="day-header__inner">
                    {{ dateLong(days[dayUri].start) }}
                    <AvatarRow
                      v-if="!days[dayUri].dayResponsibles()._meta.loading"
                      :camp-collaborations="dayResponsibleCollaborators[dayUri]"
                      max-size="20"
                    />
                    <v-skeleton-loader
                      v-else
                      type="avatar"
                      width="20"
                      height="20"
                      class="v-skeleton-loader--inherit-size"
                    />
                  </div>
                </th>
              </tr>
              <ActivityRow
                v-for="scheduleEntry in dayScheduleEntries"
                :key="scheduleEntry._meta.self"
                :schedule-entry="scheduleEntry"
                :loading-endpoints="loadingEndpoints"
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
        <caption class="text-left">
          <v-skeleton-loader type="heading" max-width="45ch" />
        </caption>
        <tbody>
          <tr class="day-header__row">
            <th colspan="5" class="day-header">
              <div class="day-header__inner">
                <v-skeleton-loader
                  type="text"
                  width="15ch"
                  class="v-skeleton-loader--no-margin"
                  style="margin: 5px 0"
                />
                <v-skeleton-loader
                  type="avatar"
                  width="20"
                  height="20"
                  class="v-skeleton-loader--inherit-size"
                />
              </div>
            </th>
          </tr>
          <ActivityRow v-for="index in 3" :key="index" />
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
import { keyBy, groupBy, mapValues, sortBy } from 'lodash'
import campCollaborationDisplayName from '../../common/helpers/campCollaborationDisplayName.js'
import { dateHelperUTCFormatted } from '@/mixins/dateHelperUTCFormatted.js'
import TextAlignBaseline from '@/components/layout/TextAlignBaseline.vue'
import { mapGetters } from 'vuex'
import {
  filterAndQueryAreEqual,
  transformValuesToHalId,
  processRouteQuery,
} from '@/helpers/querySyncHelper'
import AvatarRow from '@/components/generic/AvatarRow.vue'

function filterEquals(arr1, arr2) {
  return JSON.stringify(arr1) === JSON.stringify(arr2)
}

export default {
  name: 'Dashboard',
  components: {
    AvatarRow,
    TextAlignBaseline,
    FilterDivider,
    ActivityRow,
    SelectFilter,
    BooleanFilter,
    CategoryChip,
    ContentCard,
    UserAvatar,
  },
  mixins: [dateHelperUTCFormatted],
  props: {
    camp: { type: Function, required: true },
  },
  data() {
    return {
      loading: true,
      loadingEndpoints: {
        categories: true,
        periods: true,
        campCollaborations: true,
        progressLabels: true,
      },
      /**
       * @type {ActivityFilter} filter
       */
      filter: {
        period: null,
        responsible: [],
        category: [],
        progressLabel: [],
      },
    }
  },
  computed: {
    campCollaborations() {
      return {
        none: {
          exclusiveNone: true,
          label: this.$tc('views.camp.dashboard.responsibleNone'),
          _meta: { self: 'none' },
        },
        ...keyBy(this.camp().campCollaborations().items, '_meta.self'),
      }
    },
    categories() {
      return keyBy(this.camp().categories().items, '_meta.self')
    },
    periods() {
      return keyBy(this.camp().periods().items, '_meta.self')
    },
    progressLabels() {
      const labels = sortBy(this.camp().progressLabels().items, (l) => l.position)
      return {
        none: {
          title: this.$tc('views.camp.dashboard.progressLabelNone'),
          _meta: { self: 'none' },
        },
        ...keyBy(labels, '_meta.self'),
      }
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
    dayResponsibleCollaborators() {
      return mapValues(this.days, (day) =>
        day.dayResponsibles().items.map((item) => item.campCollaboration())
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
            }) ||
            (this.filter.responsible[0] === 'none' &&
              scheduleEntry.activity().activityResponsibles().items.length === 0)) &&
          (this.filter.progressLabel === null ||
            this.filter.progressLabel.length === 0 ||
            this.filter.progressLabel?.includes(
              scheduleEntry.activity().progressLabel?.()._meta.self ?? 'none'
            ))
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
          filterEquals(this.filter.period, null) &&
          filterEquals(this.filter.progressLabel, null)
        )
      },
      set(value) {
        this.filter.responsible = value ? [this.loggedInCampCollaboration] : []
        this.filter.category = []
        this.filter.period = null
        this.filter.progressLabel = null
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
    ...mapGetters({
      loggedInUser: 'getLoggedInUser',
    }),
  },
  watch: {
    'filter.category': 'persistRouterState',
    'filter.responsible': 'persistRouterState',
    'filter.period': 'persistRouterState',
    'filter.progressLabel': 'persistRouterState',
  },
  async mounted() {
    this.api.reload(this.camp())

    await Promise.all([
      this.api.get().days({ 'period.camp': this.camp()._meta.self }),
      ...this.camp()
        .periods()
        .items.map((period) => period.scheduleEntries()._meta.load),
      this.camp().activities()._meta.load,
    ])

    this.loading = false

    const queryFilters = processRouteQuery(this.$route.query)
    this.filter = {
      ...this.filter,
      ...queryFilters,
    }

    this.loadEndpointData('categories', 'category')
    this.loadEndpointData('campCollaborations', 'responsible', true)
    this.loadEndpointData('progressLabels', 'progressLabel', true)
    this.loadEndpointData('periods', 'period')
  },
  methods: {
    campCollaborationDisplayName(campCollaboration) {
      return campCollaborationDisplayName(campCollaboration, this.$tc.bind(this))
    },
    persistRouterState() {
      const query = transformValuesToHalId(this.filter)
      if (filterAndQueryAreEqual(query, this.$route.query)) return
      this.$router.replace({ query }).catch((err) => console.warn(err))
    },
    loadEndpointData(endpoint, filterKey, hasNone = false) {
      this.camp()
        [endpoint]()
        ._meta.load.then(({ allItems }) => {
          const collection = allItems.map((entry) => entry._meta.self)
          if (hasNone) {
            collection.push('none')
          }
          this.filter[filterKey] =
            this.filter[filterKey]?.filter((value) => collection.includes(value)) ?? null
          this.loadingEndpoints[endpoint] = false
        })
    },
  },
}
</script>

<style scoped lang="scss">
.day-header {
  z-index: 1;
  position: sticky;
  top: calc(48px - 1px - 0.75rem);
  @media #{map-get($display-breakpoints, 'sm-and-up')} {
    top: calc(0px - 1px - 0.75rem);
  }
  @media #{map-get($display-breakpoints, 'md-and-up')} {
    top: calc(64px - 1px - 0.75rem);
  }
  padding-bottom: 0.25rem;
  padding-top: 0.75rem;
  font-weight: 400;
  font-size: 0.9rem;
  text-align: left;
}

.day-header__inner {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 0 0.5rem;
  color: #5c6061;
  background: #eceff1;
  margin: 0 -16px;
  padding: 4px 16px;
  border-bottom: 1px solid #ddd;
  border-top: 1px solid #ddd;
}

.day-header__row + tr > :is(th, td) {
  border-top: 0;
}
</style>
