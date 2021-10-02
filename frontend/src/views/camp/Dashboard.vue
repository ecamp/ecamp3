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
              <h3 class="grey--text text--darken-1">
                {{ period.description }}
              </h3>
            </v-expansion-panel-header>
            <v-expansion-panel-content>
              <v-container
                fluid>
                <v-row>
                  <template v-for="day in period.days().items">
                    <template v-if="showDay(day)">
                      <v-col :key="day._meta.self" cols="12" class="pb-0">
                        <h4>{{ day.dayOffset + 1 }}) {{ displayDate(day) }}</h4>
                      </v-col>
                      <template v-for="scheduleEntry in day.scheduleEntries().items">
                        <v-col
                          v-if="showScheduleEntry(scheduleEntry)"
                          :key="scheduleEntry._meta.self"
                          :set="scheduleEntryTime = defineHelpers(scheduleEntry)"
                          cols="12" sm="6"
                          md="4" lg="3">
                          <v-card
                            :color="scheduleEntry.activity().category().color"
                            :to="scheduleEntryRoute(scheduleEntry)">
                            <v-card-title>
                              {{ scheduleEntry.activity().category().short }} {{ scheduleEntry.number }}: {{ scheduleEntry.activity().title }}
                              <v-spacer />
                              <user-avatar
                                v-for="cc in sortCampCollaborations(scheduleEntry.activity().campCollaborations().items)"
                                :key="cc._meta.self"
                                :camp-collaboration="cc" :size="24"
                                style="margin: 2px" />
                            </v-card-title>
                            <v-card-subtitle>
                              {{ $date.utc(scheduleEntryTime.startTime).format($tc('global.datetime.dateShort')) == $date.utc(scheduleEntryTime.endTime).format($tc('global.datetime.dateShort'))
                                ? '' : $date.utc(scheduleEntryTime.startTime).format($tc('global.datetime.dateShort')) }}
                              <b> {{ $date.utc(scheduleEntryTime.startTime).format($tc('global.datetime.hourShort')) }} </b>
                              -
                              {{ $date.utc(scheduleEntryTime.startTime).format($tc('global.datetime.dateShort')) == $date.utc(scheduleEntryTime.endTime).format($tc('global.datetime.dateShort'))
                                ? '' : $date.utc(scheduleEntryTime.endTime).format($tc('global.datetime.dateShort')) }}
                              <b> {{ $date.utc(scheduleEntryTime.endTime).format($tc('global.datetime.hourShort')) }} </b>
                            </v-card-subtitle>
                          </v-card>
                        </v-col>
                      </template>
                    </template>
                  </template>
                </v-row>
              </v-container>
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
import { defineHelpers } from '@/common/helpers/scheduleEntry/dateHelperUTC.js'

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
    defineHelpers,
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
    displayDate (day) {
      const date = Date.parse(day.period().start) + day.dayOffset * 24 * 60 * 60 * 1000
      return this.$date.utc(date).format(this.$tc('global.datetime.dateLong'))
    },
    showScheduleEntry (scheduleEntry) {
      const authUser = this.$auth.user()
      const campCollaborations = scheduleEntry.activity().campCollaborations().items
      return campCollaborations.some(cc => cc.user().id === authUser.id)
    },
    sortCampCollaborations (campCollaborations) {
      return campCollaborations.sort((a, b) => parseInt(a.user().id, 16) - parseInt(b.user().id, 16))
    }
  }
}
</script>

<style scoped>
</style>
