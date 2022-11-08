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
          v-model="filter.collaborator"
          multiple
          :items="users"
          display-field="displayName"
          :label="$tc('views.camp.dashboard.responsible')"
        >
          <template #item="{ item }">
            <UserAvatar :user="users[item.value]" size="18" class="mr-1" />
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
          v-if="results.length > 1"
          v-model="filter.period"
          :items="periods"
          display-field="description"
          :label="$tc('views.camp.dashboard.period')"
        />
        <v-chip
          v-if="
            filter.period ||
            (filter.collaborator && filter.collaborator.length > 0) ||
            filter.category
          "
          label
          outlined
          @click="
            filter = {
              period: '',
              collaborator: [],
              category: '',
            }
          "
        >
          <v-icon left>mdi-close</v-icon>
          {{ $tc('views.camp.dashboard.clearFilters') }}
        </v-chip>
      </div>
      <table
        v-for="period in events"
        :key="period.id"
        class="mx-4 mt-6 mb-3"
        style="border-collapse: collapse"
      >
        <caption class="font-weight-bold text-left">
          {{
            period.id
          }}
        </caption>
        <thead :key="period.id + '_head'">
          <tr class="d-sr-only">
            <th :id="period.id + 'th-number'" scope="col">
              {{ $tc('views.camp.dashboard.columns.number') }}
            </th>
            <th :id="period.id + 'th-category'" scope="col">
              {{ $tc('views.camp.dashboard.columns.category') }}
            </th>
            <th :id="period.id + 'th-time'" scope="col">
              {{ $tc('views.camp.dashboard.columns.time') }}
            </th>
            <th :id="period.id + 'th-title'" scope="col">
              {{ $tc('views.camp.dashboard.columns.title') }}
            </th>
            <th :id="period.id + 'th-responsible'" scope="col">
              {{ $tc('views.camp.dashboard.columns.responsible') }}
            </th>
          </tr>
        </thead>
        <tbody
          v-for="day in period.days"
          :key="period.id + day.id"
          :aria-labelledby="period.id + day.id + 'th'"
        >
          <tr>
            <th
              :id="period.id + day.id + 'th'"
              colspan="5"
              scope="colgroup"
              align="left"
              style="
                padding-top: 0.75rem;
                font-weight: 400;
                color: #666;
                font-size: 0.9rem;
              "
            >
              {{ day.date }}
            </th>
          </tr>
          <ActivityRow
            v-for="scheduleEntry in day.scheduleEntries"
            :key="day.id + scheduleEntry.id"
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
import {
  dateShort,
  dateLong,
  hourShort,
} from '@/common/helpers/dateHelperUTCFormatted.js'
import CategoryChip from '@/components/generic/CategoryChip.vue'
import BooleanFilter from '@/components/dashboard/BooleanFilter.vue'
import SelectFilter from '@/components/dashboard/SelectFilter.vue'
import ActivityRow from '@/components/dashboard/ActivityRow.vue'
import dayjs from 'dayjs'
import FilterDivider from '@/components/dashboard/FilterDivider.vue'
import { keyBy } from 'lodash'

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
      openPeriods: [],
      filter: {
        period: null,
        collaborator: [],
        category: [],
      },
      results: [
        {
          id: 'Vorlager',
          days: [
            {
              id: 1,
              scheduleEntries: [
                {
                  id: 1,
                  number: '1.1',
                  start: '18',
                  duration: '2h',
                  title: 'Stafetten',
                  location: 'Sportfeld',
                  collaborators: ['Ikarus', 'Forte', 'Linux'],
                  category: 'LS',
                },
                {
                  id: 2,
                  number: '1.2',
                  start: '20',
                  duration: '1h',
                  title: 'Essen',
                  location: 'Aufenthaltsraum',
                  collaborators: ['Forte'],
                  category: 'ES',
                },
                {
                  id: 3,
                  number: '1.3',
                  start: '21',
                  duration: '30m',
                  title: 'Once upon a time there was a long title',
                  location: 'Gschichtliruum',
                  collaborators: ['Cosinus'],
                  category: 'LS',
                },
              ],
            },
          ],
        },
        {
          id: 'Hauptlager',
          days: [
            {
              id: 2,
              scheduleEntries: [
                {
                  id: 1,
                  number: '2.1',
                  start: '8',
                  duration: '2h',
                  title: 'Morgenfit',
                  location: 'Wiese',
                  collaborators: ['Linux', 'Smiley'],
                  category: 'LA',
                },
                {
                  id: 2,
                  number: '2.2',
                  start: '10',
                  duration: '1h',
                  title: 'Atelier',
                  location: 'Aufenthaltsraum',
                  collaborators: ['Cosinus', 'Linux'],
                  category: 'LP',
                },
                {
                  id: 3,
                  number: '2.3',
                  start: '12',
                  duration: '30m',
                  title: 'Zmittag',
                  location: 'Aufenthaltsraum',
                  collaborators: ['Ikarus'],
                  category: 'ES',
                },
              ],
            },
            {
              id: 3,
              scheduleEntries: [
                {
                  id: 1,
                  number: '3.1',
                  start: '10',
                  duration: '7h',
                  title: 'Geländegame',
                  location: 'Wald',
                  collaborators: ['Olippo', 'Forte', 'Linux'],
                  category: 'LS',
                },
                {
                  id: 2,
                  number: '3.2',
                  start: '17',
                  duration: '4h',
                  title: 'Partyznacht',
                  location: 'Aufenthaltsraum',
                  collaborators: ['Linux'],
                  category: 'ES',
                },
                {
                  id: 3,
                  number: '3.3',
                  start: '21',
                  duration: '30m',
                  title: 'Tabs',
                  location: 'Feuerschale',
                  collaborators: ['Smiley'],
                  category: 'TA',
                },
              ],
            },
          ],
        },
      ],
    }
  },
  computed: {
    users() {
      return keyBy(
        this.camp()
          .campCollaborations()
          .items.map((collaboration) => {
            if (collaboration.user === null) {
              // Unregistered invited user
              return {
                displayName: collaboration.inviteEmail,
                _meta: collaboration._meta,
              }
            }
            return collaboration.user()
          }),
        '_meta.self'
      )
    },
    categories() {
      return keyBy(this.camp().categories().items, '_meta.self')
    },
    periods() {
      return keyBy(this.camp().periods().items, '_meta.self')
    },
    events() {
      return this.results
        .filter(
          (period) =>
            this.filter.period.length === 0 ||
            (Array.isArray(this.filter.period)
              ? this.filter.period.some((filter) => period.id.includes(filter))
              : period.id.includes(this.filter.period))
        )
        .map((period) => ({
          ...period,
          days: period.days
            .map(({ id, scheduleEntries }) => ({
              id,
              date: dayjs().add(id, 'day').format('dd, D. MMM YYYY'),
              scheduleEntries: scheduleEntries
                .filter((scheduleEntry) => {
                  return this.filterScheduleEntry(scheduleEntry)
                })
                .map((scheduleEntry) => ({
                  ...scheduleEntry,
                  collaborators: scheduleEntry.collaborators.map(
                    (collaborator) => this.users[collaborator]
                  ),
                  category: this.categories[scheduleEntry.category],
                })),
            }))
            .filter(({ scheduleEntries }) => scheduleEntries.length > 0),
        }))
        .filter(({ days }) => days.length > 0)
    },
    // returns scheduleEntries per day without the need for an additional API call for each day
    scheduleEntriesByDay() {
      let scheduleEntriesByDay = []

      this.camp()
        .periods()
        .items.forEach(function (period) {
          period.scheduleEntries().items.forEach(function (scheduleEntry) {
            const dayUri = scheduleEntry.day()._meta.self
            if (!(dayUri in scheduleEntriesByDay)) {
              scheduleEntriesByDay[dayUri] = []
            }
            scheduleEntriesByDay[dayUri].push(scheduleEntry)
          })
        })

      return scheduleEntriesByDay
    },
    showOnlyMyActivities: {
      get() {
        return (
          this.filter.collaborator?.includes(this.loggedInUser?._meta?.self) &&
          this.filter.collaborator?.length === 1
        )
      },
      set(value) {
        this.filter.collaborator = value ? [this.loggedInUser._meta.self] : []
      },
    },
  },
  async mounted() {
    const [loggedInUser, periods] = await Promise.all([
      this.$auth.loadUser(),
      this.camp().periods()._meta.load,
      this.camp().activities()._meta.load,
      this.camp().categories()._meta.load,
    ])

    this.openPeriods = periods.items
      .map((period, idx) => (Date.parse(period.end) >= new Date() ? idx : null))
      .filter((idx) => idx !== null)

    this.loggedInUser = loggedInUser
    this.loading = false
  },
  methods: {
    dayjs,
    dateShort,
    dateLong,
    hourShort,
    matchCollaborators: function (scheduleEntry) {
      return (
        this.filter.collaborator === null ||
        this.filter.collaborator.length === 0 ||
        this.filter.collaborator.every((collaborator) =>
          scheduleEntry.collaborators.some((col) => col === collaborator)
        )
      )
    },
    matchCategory: function (scheduleEntry) {
      return scheduleEntry.category.includes(this.filter.category)
    },
    filterScheduleEntry(scheduleEntry) {
      return this.matchCollaborators(scheduleEntry) && this.matchCategory(scheduleEntry)
    },
  },
}
</script>

<style scoped></style>
