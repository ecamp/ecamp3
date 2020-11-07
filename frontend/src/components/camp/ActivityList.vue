<!--
Lists all activity instances in a list view.
-->

<template>
  <v-list dense>
    <template v-for="scheduleEntry in scheduleEntries">
      <v-skeleton-loader
        v-if="scheduleEntry.activity()._meta.loading"
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
          <v-list-item-title>{{ scheduleEntry.activity().title }}</v-list-item-title>
          <v-list-item-subtitle>{{ $moment.utc(scheduleEntry.startTime) }} - {{ $moment.utc(scheduleEntry.endTime) }}</v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
    </template>
  </v-list>
</template>
<script>
import { scheduleEntryRoute } from '@/router'
import { defineHelpers } from '@/components/scheduleEntry/dateHelperUTC'

export default {
  name: 'ActivityList',
  props: {
    period: {
      type: Function,
      required: true
    }
  },
  computed: {
    camp () {
      return this.period().camp()
    },
    scheduleEntries () {
      return this.period().scheduleEntries().items.map((entry) => defineHelpers(entry, false))
    }
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
