<!--
Admin screen of a camp: Displays details & periods of a single camp and allows to edit them.
-->

<template>
  <content-card :title="camp().title" toolbar>

    <template #title>

      <v-card-text>
        <ETextField
          v-model="filter.title"
          placeholder="Aktivitäten suchen"
          rounded
          outlined
        />
        <div class="d-flex my-2" style="gap: 0.5rem; overflow-x: scroll">
          <ESelect
            outlined
            rounded
            v-model="filter.collaborator"
            placeholder="Collaborator"
            multiple
            dense
            :items="[{text: 'Alle', value: ''}, 'Forte', 'Linux', 'Smiley', 'Olippo', 'Cosinus', 'Ikarus']"
            chips
          />
          <div></div>
          <ESelect
            outlined
            rounded
            v-model="filter.category"
            placeholder="Category"
            multiple
            dense
            :items="['LS', 'LA', 'TABS', 'ES']"
            chips
          />
          <div></div>
          <ESelect
            outlined
            rounded
            v-model="filter.period"
            placeholder="Period"
            multiple
            dense
            :items="[{text: 'Alle', value: ''}, {text:'Vorlager', value:'Vorlager'}, {text: 'Hauptlager', value: 'Hauptlager'}]"
          />
          <div></div>
          <v-btn
            rounded
            outlined
            elevation="4"
            @click="
            filter = {
              period: '',
              days: '',
              collaborator: [],
              category: '',
              title: '',
            }
          "
          >Reset filters<v-icon small right>mdi-filter-remove</v-icon></v-btn
          >
        </div>
      </v-card-text>
    </template>
    <v-card-text>
      {{ $tc('views.camp.dashboard.viewDescription', 1, { title: camp().title }) }}
    </v-card-text>

    <v-skeleton-loader v-if="loading" type="article" />


      <div style="display: flow-root">
        <table style="width: -moz-fit-content; width: -webkit-fill-available">
          <thead>
            <tr>
              <th>Nr</th>
              <th>Cat.</th>
              <th align="right" style="padding-right: 10px">Duration</th>
              <th align="right" style="padding-right: 10px">Time</th>
              <th align="left" width="100%">Title</th>
              <th align="left">Collaborators</th>
            </tr>
          </thead>
          <template v-for="period in events">
            <thead :key="period.id + '_head'">
              <tr>
                <th
                  colspan="6"
                  style="
                    text-align: left;
                    padding-top: 10px;
                    border-bottom: 1px solid grey;
                  "
                >
                  {{ period.id }}
                </th>
              </tr>
            </thead>
            <tbody v-for="day in period.days" :key="period.id + '_body'">
              <tr v-for="scheduleEntry in day.scheduleEntries">
                <th align="left" class="tabular-nums">{{ day.id }}.{{ scheduleEntry.id }}</th>

                <td align="center">
                  <v-chip
                    :color="categories[scheduleEntry.category]"
                    class="e-category-chip tabular-nums"
                    dark
                    small
                  >
                    {{ scheduleEntry.category }}
                  </v-chip>
                </td>

                <td align="right" style="padding-right: 10px">
                  {{ scheduleEntry.duration }}
                </td>

                <td align="right" style="padding-right: 10px">
                  {{ scheduleEntry.start }}:00
                </td>
                <td>
                  <b>{{ scheduleEntry.title }}</b>
                </td>
                <td align="right">{{ scheduleEntry.collaborators.join(', ') }}</td>
              </tr>
            </tbody>
          </template>
        </table>
      </div>
    </v-card-text>
  </content-card>
</template>

<script>
import { campRoute, scheduleEntryRoute } from '@/router.js'
import ContentCard from '@/components/layout/ContentCard.vue'
import UserAvatar from '../../components/user/UserAvatar.vue'
import {
  dateShort,
  dateLong,
  hourShort,
} from '@/common/helpers/dateHelperUTCFormatted.js'
import { sortBy } from 'lodash'
import campCollaborationDisplayName from '@/common/helpers/campCollaborationDisplayName.js'
import CategoryChip from '@/components/story/CategoryChip.vue'

export default {
  name: 'Dashboard',
  components: {
    CategoryChip,
    ContentCard,
    UserAvatar,
  },
  props: {
    camp: { type: Function, required: true },
  },
  data() {
    return {
      loading: true,
      openPeriods: [],
      filter: {
        period: '',
        days: '',
        collaborator: ['Linux'],
        category: '',
        title: '',
      },
      categories: {
        LS: 'red',
        LA: '#4caf50',
        TABS: 'red',
        ES: '#11a1e1',
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
                  start: '18',
                  duration: '2h',
                  title: 'Stafetten',
                  collaborators: ['Ikarus', 'Forte'],
                  category: 'LS',
                },
                {
                  id: 2,
                  start: '20',
                  duration: '1h',
                  title: 'Essen',
                  collaborators: ['Linux'],
                  category: 'ES',
                },
                {
                  id: 3,
                  start: '21',
                  duration: '30m',
                  title: 'c',
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
                  start: '8',
                  duration: '2h',
                  title: 'Morgenfit',
                  collaborators: ['Linux', 'Smiley'],
                  category: 'LA',
                },
                {
                  id: 2,
                  start: '10',
                  duration: '1h',
                  title: 'Atelier',
                  collaborators: ['Cosinus'],
                  category: 'LP',
                },
                {
                  id: 3,
                  start: '12',
                  duration: '30m',
                  title: 'Zmittag',
                  collaborators: ['Forte'],
                  category: 'ES',
                },
              ],
            },
            {
              id: 3,
              scheduleEntries: [
                {
                  id: 1,
                  start: '10',
                  duration: '7h',
                  title: 'Geländegame',
                  collaborators: ['Olippo', 'Forte'],
                  category: 'LS',
                },
                {
                  id: 2,
                  start: '17',
                  duration: '4h',
                  title: 'Partyznacht',
                  collaborators: ['Linux'],
                  category: 'ES',
                },
                {
                  id: 3,
                  start: '21',
                  duration: '30m',
                  title: 'TABS',
                  collaborators: ['Smiley'],
                  category: 'TABS',
                },
              ],
            },
          ],
        },
      ],
    }
  },
  computed: {
    events() {
      return this.results
        .filter((period) =>
          Array.isArray(this.filter.period)
            ? this.filter.period.some((filter) => period.id.includes(filter))
            : period.id.includes(this.filter.period)
        )
        .map((period) => ({
          ...period,
          days: period.days.map(({ id, scheduleEntries }) => ({
            id,
            scheduleEntries: scheduleEntries.filter((scheduleEntry) => {
              return (
                scheduleEntry.title.includes(this.filter.title) &&
                (this.filter.category === ''
                  ? true
                  : Array.isArray(this.filter.category)
                  ? this.filter.category.some(
                      (category) => scheduleEntry.category === category
                    )
                  : scheduleEntry.category.includes(this.filter.category)) &&
                (this.filter.collaborator === ''
                  ? true
                  : Array.isArray(this.filter.collaborator)
                  ? this.filter.collaborator.every((collaborator) =>
                      scheduleEntry.collaborators.some((col) => col === collaborator)
                    )
                  : scheduleEntry.collaborators.includes(this.filter.collaborator)) &&
                scheduleEntry.category.includes(this.filter.category)
              )
            }),
          })),
        }))
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
  },
  async mounted() {
    const [periods] = await Promise.all([
      this.camp().periods()._meta.load,
      this.camp().activities()._meta.load,
      this.camp().categories()._meta.load,
    ])

    this.openPeriods = periods.items
      .map((period, idx) => (Date.parse(period.end) >= new Date() ? idx : null))
      .filter((idx) => idx !== null)

    this.loading = false
  },
  methods: {
    dateShort,
    dateLong,
    hourShort,
    campRoute,
    scheduleEntryRoute,
    showAnyPeriod(camp) {
      return camp.periods().items.some(this.showPeriod)
    },
    showPeriod(period) {
      return period.days().items.some(this.showDay)
    },
    showDay(day) {
      return (this.scheduleEntriesByDay[day._meta.self] || []).some(
        this.showScheduleEntry
      )
    },
    showScheduleEntry(scheduleEntry) {
      const authUser = this.$auth.user()
      const activityResponsibles = scheduleEntry.activity().activityResponsibles().items
      return activityResponsibles.some((activityResponsible) => {
        const campCollaboration = activityResponsible.campCollaboration()
        return (
          !campCollaboration._meta.loading &&
          typeof campCollaboration.user === 'function' &&
          campCollaboration.user().id === authUser.id
        )
      })
    },
    sortActivityResponsibles(activityResponsibles) {
      return sortBy(activityResponsibles, (activityResponsible) =>
        campCollaborationDisplayName(activityResponsible.campCollaboration(), null, false)
      )
    },
  },
}
</script>

<style scoped></style>
