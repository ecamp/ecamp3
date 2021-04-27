<!--
Admin screen of a camp: Displays details & periods of a single camp and allows to edit them.
-->

<template>
  <content-card :title="camp().title" toolbar>
    <v-expansion-panels v-model="openPeriods" multiple
                        flat accordion>
      <v-expansion-panel
        v-for="period in camp().periods().items"
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
                        :to="scheduleEntryRoute(camp(), scheduleEntry)">
                        <v-card-title>
                          {{ scheduleEntry.activity().category().short }} {{ scheduleEntry.number }}: {{ scheduleEntry.activity().title }}
                          <v-spacer />
                          <user-avatar
                            v-for="cc in sortCampCollaborations(scheduleEntry.activity().campCollaborations().items)"
                            :key="cc._meta.self"
                            :value="cc" :size="24"
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
    </v-expansion-panels>
  </content-card>
</template>

<script>
import { scheduleEntryRoute } from '@/router.js'
import ContentCard from '@/components/layout/ContentCard.vue'
import UserAvatar from '../../components/user/UserAvatar.vue'
import { defineHelpers } from '@/components/scheduleEntry/dateHelperUTC.js'

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
    scheduleEntryRoute,
    showDay (day) {
      return day.scheduleEntries().items.some(this.showScheduleEntry)
    },
    displayDate (day) {
      const date = Date.parse(day.period().start) + day.dayOffset * 24 * 60 * 60 * 1000
      return this.$date.utc(date).format(this.$tc('global.datetime.dateLong'))
    },
    showScheduleEntry (scheduleEntry) {
      const authUser = this.api.get().authUser()
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
