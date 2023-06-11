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
              <TextAlignBaseline class="mr-1">
                <UserAvatar
                  :camp-collaboration="campCollaborations[item.value]"
                  size="20"
                />
              </TextAlignBaseline>
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
import { groupBy, keyBy, mapValues } from 'lodash'
import campCollaborationDisplayName from '../../common/helpers/campCollaborationDisplayName.js'
import { dateHelperUTCFormatted } from '@/mixins/dateHelperUTCFormatted.js'
import TextAlignBaseline from '@/components/layout/TextAlignBaseline.vue'
import { halUriToId, idToHalUri } from '@/helpers/formatHalHelper.js'
import { mapGetters } from 'vuex'

function filterEquals(arr1, arr2) {
  return JSON.stringify(arr1) === JSON.stringify(arr2)
}
/**
 * Allowed Url param keys
 * @typedef {'period'|'responsible'|'category'} UrlParamKey
 */
/**
 * The Allowed Url parameter keys
 * @type {UrlParamKey[]} UrlParamKeys
 */
const urlParamKeys = ['period', 'responsible', 'category']

/**
 * Map for url param keys to hal types
 * @type {{(typeof urlParamKeys[number]):HalType }}
 */
const URL_PARAM_TO_HAL_TYPE = {
  category: 'categories',
  responsible: 'camp_collaborations',
  period: 'periods',
}
export default {
  name: 'Dashboard',
  components: {
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
      isActive: false,
      /**
       * Contains the Values declared initially. In the unverified values from the Url params will be written in the filter.url
       * @type {{period:null|HalUri,responsible:HalUri[],category: HalUri[],url?:{period?:null|HalUri,responsible?:HalUri[],category?: HalUri[]}}} Filter*/
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
    urlQuery() {
      return Object.fromEntries(
        Object.entries({
          responsible: this.filter.responsible,
          category: this.filter.category,
          period: this.filter.period,
        })
          .filter(([_, value]) => !!value)
          .map(([key, value]) => [
            key,
            Array.isArray(value) ? value.map((e) => halUriToId(e)) : halUriToId(value),
          ])
      )
    },
    ...mapGetters({
      loggedInUser: 'getLoggedInUser',
    }),
    /**
     * Is True until the Component gets unmounted or there is a Navigation to another page
     */
    syncUrlQuerActive() {
      return this.isActive && this.$router.currentRoute.name === 'camp/dashboard'
    },
  },
  watch: {
    'filter.category': 'persistRouterState',
    'filter.responsible': 'persistRouterState',
    'filter.period': 'persistRouterState',
  },
  async mounted() {
    this.isActive = true
    this.api.reload(this.camp())

    const loadingDataPromise = Promise.all([
      this.loggedInUser._meta.load,
      this.camp().activities()._meta.load,
    ])

    // Once camp data is loaded validate and map values from url param to filter
    await Promise.all([
      this.camp().categories()._meta.load,
      this.camp().periods()._meta.load,
      this.camp().campCollaborations()._meta.load,
      loadingDataPromise,
    ]).then(([categories, periods, collaborators]) => {
      const availableCategoryIds = categories.allItems.map((value) => value._meta.self)
      const availablePeriodsIds = periods.allItems.map((value) => value._meta.self)
      const availableCollaboratorIds = collaborators.allItems.map(
        (value) => value._meta.self
      )

      const category = (this.filter.url?.category ?? []).filter((value) =>
        availableCategoryIds.includes(value)
      )
      const responsible = (this.filter.url?.responsible ?? []).filter((value) =>
        availableCollaboratorIds.includes(value)
      )
      const period = availablePeriodsIds.includes(this.filter.url?.period)
        ? this.filter.url.period
        : null

      if (!this.syncUrlQuerActive) {
        alert('Sync Aborted')
        return
      }

      this.filter = {
        category,
        responsible,
        period,
      }
    })

    this.loading = false
  },
  beforeMount() {
    this.filter.url = Object.fromEntries(
      /**
       * @type {[UrlParamKey,(HalUri[])|HalUri]}
       */
      Object.entries(this.$route.query)
        .filter(([key, value]) => urlParamKeys.includes(key) && !!value)
        .map(([key, value]) => [key, value, URL_PARAM_TO_HAL_TYPE[key]])
        .map(([key, value, type]) => {
          if (typeof value === 'string') {
            let halUriValue =
              key === 'period' ? idToHalUri(type, value) : [idToHalUri(type, value)]
            return [key, halUriValue]
          }
          if (Array.isArray(value)) {
            let uriValues = value
              .filter((entry) => !!entry)
              .map((entry) => idToHalUri(type, entry))
            return [key, uriValues]
          }
          return [key, null]
        })
        .filter(([_, value]) => {
          return !!value
        })
    )
  },
  methods: {
    campCollaborationDisplayName(campCollaboration) {
      return campCollaborationDisplayName(campCollaboration, this.$tc.bind(this))
    },
    persistRouterState() {
      let query = this.urlQuery
      if (filterEquals(query, this.$route.query)) return
      if (!this.syncUrlQuerActive) {
        alert('AAA')
        return
      }
      this.$router.replace({ append: true, query }).then((value) => console.log(value))
    },
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
