<!--
Lists all activity instances in a list view.
-->

<template>
  <v-list v-if="scheduleEntries.length > 0" dense>
    <template v-for="scheduleEntry in scheduleEntries">
      <v-skeleton-loader
        v-if="activitiesLoading || scheduleEntry.activity()._meta.loading"
        :key="scheduleEntry._meta.self"
        type="list-item-avatar-two-line" height="60" />
      <v-list-item
        v-else
        :key="scheduleEntry._meta.self"
        two-line
        :to="scheduleEntryLink(scheduleEntry)">
        <v-chip class="mr-2" :color="scheduleEntry.activity().activityCategory().color.toString()">
          {{
            scheduleEntry.activity().activityCategory().short
          }}
        </v-chip>
        <v-list-item-content>
          <v-list-item-title>{{ scheduleEntry.number }}: {{ scheduleEntry.activity().title }}</v-list-item-title>
          <v-list-item-subtitle>{{ $moment.utc(scheduleEntry.startTime) }} - {{ $moment.utc(scheduleEntry.endTime) }}</v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
    </template>
  </v-list>
</template>
<script>
import { scheduleEntryRoute } from '@/router'

export default {
  name: 'ActivityList',
  props: {
    period: {
      type: Function,
      required: true
    },
    scheduleEntries: {
      type: Array,
      required: true
    }
  },
  data () {
    return {
      activitiesLoading: true
    }
  },
  computed: {
    camp () {
      return this.period().camp()
    }
  },
  mounted () {
    this.api.get().activities({ periodId: this.period().id })._meta.load.then(() => { this.activitiesLoading = false })
  },
  methods: {
    scheduleEntryLink (scheduleEntry) {
      return scheduleEntryRoute(this.camp, scheduleEntry)
    }
  }
}
</script>
<style lang="scss">

</style>
