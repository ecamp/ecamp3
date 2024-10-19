<template>
  <content-card :title="$tc('views.camp.dashboard.activities')" toolbar>
    <template #title-actions>
      <v-spacer />
      <v-btn v-if="today !== null" :icon="true" @click="scrollToToday">
        <v-icon>mdi-calendar-today</v-icon>
      </v-btn>
    </template>
    <div class="d-flow-root">
      <ScheduleEntryFilters
        v-if="loading"
        key="loadingstate"
        class="ma-4"
        :loading-endpoints="true"
        :camp="camp"
        :periods="periods"
      />
      <ScheduleEntryFilters
        v-else
        key="filterstate"
        v-model="filter"
        class="ma-4"
        :loading-endpoints="loadingEndpoints"
        :camp="camp"
        :periods="periods"
      />
      <template v-if="!loading">
        <table
          v-for="(periodDays, uri) in groupedScheduleEntries"
          :key="uri"
          class="mx-4 mt-6 mb-3"
          style="border-collapse: collapse"
        >
          <caption class="text-left">
            <router-link
              :to="periodRoute(periods[uri])"
              class="text-decoration-none text-decoration-hover-underline black--text font-weight-bold"
            >
              {{ periods[uri].description }}
            </router-link>
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
              <tr :ref="days[dayUri].id" class="day-header__row">
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
import { campRoute, periodRoute } from '@/router.js'
import ContentCard from '@/components/layout/ContentCard.vue'
import ActivityRow from '@/components/dashboard/ActivityRow.vue'
import { keyBy, groupBy, mapValues } from 'lodash'
import { dateHelperUTCFormatted } from '@/mixins/dateHelperUTCFormatted.js'
import { mapGetters } from 'vuex'
import {
  filterAndQueryAreEqual,
  transformValuesToHalId,
  processRouteQuery,
} from '@/helpers/querySyncHelper'
import AvatarRow from '@/components/generic/AvatarRow.vue'
import ScheduleEntryFilters from '@/components/program/ScheduleEntryFilters.vue'
import dayjs from '@/common/helpers/dayjs.js'

export default {
  name: 'Dashboard',
  components: {
    ScheduleEntryFilters,
    AvatarRow,
    ActivityRow,
    ContentCard,
  },
  mixins: [dateHelperUTCFormatted],
  props: {
    camp: { type: Object, required: true },
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
    periods() {
      return keyBy(this.camp.periods().items, '_meta.self')
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
    today() {
      const now = dayjs.utc()
      const today = Object.values(this.days).filter(
        (d) => dayjs.utc(d.start) <= now && dayjs.utc(d.end) >= now
      )
      return today.length > 0 ? today[0] : null
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
    await Promise.all([
      this.camp._meta.load,
      this.api.get().days({ 'period.camp': this.camp._meta.self }),
      ...this.camp.periods().items.map((period) => period.scheduleEntries()._meta.load),
      this.camp.activities()._meta.load,
    ])

    this.loading = false

    const queryFilters = processRouteQuery(this.$route.query)
    Object.entries(queryFilters).forEach(([key, value]) => {
      this.filter[key] = value
    })

    this.camp.periods()._meta.load.then(({ allItems }) => {
      const collection = allItems.map((entry) => entry._meta.self)
      this.filter.periods =
        this.filter.periods?.filter((value) => collection.includes(value)) ?? null
      this.loadingEndpoints.periods = false
    })
  },
  methods: {
    campRoute,
    periodRoute,
    persistRouterState() {
      const query = transformValuesToHalId(this.filter)
      if (filterAndQueryAreEqual(query, this.$route.query)) return
      this.$router.replace({ query }).catch((err) => console.warn(err))
    },
    scrollToToday() {
      const refs = this.$refs[this.today.id]
      if (refs.length > 0) {
        refs[0].scrollIntoView({ behavior: 'smooth' })
      }
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

.text-decoration-hover-underline:hover {
  text-decoration: underline !important;
}
</style>
