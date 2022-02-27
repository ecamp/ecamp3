<!--
Admin screen of a camp: Displays details & periods of a single camp and allows to edit them.
-->

<template>
  <content-card :title="camp().title" toolbar>
    <v-card-text>
      {{ $tc('views.camp.dashboard.viewDescription', 1, { title: camp().title }) }}
    </v-card-text>

    <template v-if="showAnyPeriod(camp())">
      <v-expansion-panels v-model="openPeriods" multiple
                          flat accordion>
        <template v-for="period in camp().periods().items">
          <v-expansion-panel
            v-if="showPeriod(period)"
            :key="period._meta.self">
            <v-expansion-panel-header>
              <h3>
                {{ period.description }}
              </h3>
            </v-expansion-panel-header>
            <v-expansion-panel-content>
              <v-row>
                <template v-for="day in period.days().items">
                  <template v-if="showDay(day)">
                    <v-col :key="day._meta.self" cols="12" class="pb-0">
                      <h4>{{ day.dayOffset + 1 }}) {{ dateLong(day.start) }}</h4>
                    </v-col>
                    <template v-for="scheduleEntry in day.scheduleEntries().items">
                      <v-col
                        v-if="showScheduleEntry(scheduleEntry)"
                        :key="scheduleEntry._meta.self"
                        cols="12" sm="6"
                        md="4" lg="3">
                        <v-card
                          :color="scheduleEntry.activity().category().color"
                          :to="scheduleEntryRoute(scheduleEntry)">
                          <v-card-title>
                            {{ scheduleEntry.activity().category().short }} {{ scheduleEntry.number }}: {{ scheduleEntry.activity().title }}
                            <v-spacer />
                            <user-avatar
                              v-for="ar in sortCampCollaborations(scheduleEntry.activity().activityResponsibles().items)"
                              :key="ar._meta.self"
                              :camp-collaboration="ar.campCollaboration()" :size="24"
                              style="margin: 2px" />
                          </v-card-title>
                          <v-card-subtitle>
                            {{ dateShort(scheduleEntry.start) == dateShort(scheduleEntry.end)
                              ? '' : dateShort(scheduleEntry.start) }}
                            <b> {{ hourShort(scheduleEntry.start) }} </b>
                            -
                            {{ dateShort(scheduleEntry.start) == dateShort(scheduleEntry.end)
                              ? '' : dateShort(scheduleEntry.end) }}
                            <b> {{ hourShort(scheduleEntry.end) }} </b>
                          </v-card-subtitle>
                        </v-card>
                      </v-col>
                    </template>
                  </template>
                </template>
              </v-row>
            </v-expansion-panel-content>
          </v-expansion-panel>
        </template>
      </v-expansion-panels>
    </template>
    <template v-else>
      <v-card-text>
        <p style="text-align: center">
          {{ $tc('views.camp.dashboard.noActivitiesLine1') }} <br>
          {{ $tc('views.camp.dashboard.noActivitiesLine2') }} <br>
        </p>
        <p style="text-align: center">
          <v-btn color="success" :to="campRoute(camp(), 'program')">
            <v-icon size="150%" left>
              mdi-view-dashboard-variant
            </v-icon>
            {{ $tc('views.camp.dashboard.program') }}
          </v-btn>
        </p>
      </v-card-text>
    </template>
  </content-card>
</template>

<script>
import { campRoute, scheduleEntryRoute } from '@/router.js'
import ContentCard from '@/components/layout/ContentCard.vue'
import UserAvatar from '../../components/user/UserAvatar.vue'
import { dateShort, dateLong, hourShort } from '@/common/helpers/scheduleEntry/dateHelperUTCFormatted.js'

export default {
  name: 'Dashboard',
  components: {
    ContentCard,
    UserAvatar
  },
  props: {
    camp: { type: Function, required: true }
  },
  data () {
    return {
      openPeriods: []
    }
  },
  mounted () {
    this.camp().periods()._meta.load.then(periods => {
      this.openPeriods = periods.items
        .map((period, idx) => Date.parse(period.end) >= new Date() ? idx : null)
        .filter(idx => idx !== null)
    })
    this.api.reload(this.camp())
  },
  methods: {
    dateShort,
    dateLong,
    hourShort,
    campRoute,
    scheduleEntryRoute,
    showAnyPeriod (camp) {
      return camp.periods().items.some(this.showPeriod)
    },
    showPeriod (period) {
      return period.days().items.some(this.showDay)
    },
    showDay (day) {
      return day.scheduleEntries().items.some(this.showScheduleEntry)
    },
    showScheduleEntry (scheduleEntry) {
      const authUser = this.$auth.user()
      const activityResponsibles = scheduleEntry.activity().activityResponsibles().items
      return activityResponsibles.some(ar => ar.campCollaboration().user().id === authUser.id)
    },
    sortCampCollaborations (campCollaborations) {
      return campCollaborations.sort((a, b) => parseInt(a.user().id, 16) - parseInt(b.user().id, 16))
    }
  }
}
</script>

<style scoped>
</style>
