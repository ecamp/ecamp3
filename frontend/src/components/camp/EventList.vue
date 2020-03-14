<!--
Listing all given event instances in a calendar view.
-->

<template>
  <v-list dense>
    <template v-for="eventInstance in eventInstances">
      <v-skeleton-loader
        v-if="eventInstance.event()._meta.loading"
        :key="eventInstance._meta.self"
        type="list-item-avatar-two-line" height="60" />
      <v-list-item
        v-else
        :key="eventInstance._meta.self"
        two-line
        :to="eventInstanceLink(eventInstance)">
        <v-chip class="mr-2" :color="eventInstance.event().event_category().color.toString()">{{ eventInstance.event().event_category().short }}</v-chip>
        <v-list-item-content>
          <v-list-item-title>{{ eventInstance.event().title }}</v-list-item-title>
          <v-list-item-subtitle>{{ eventInstance.start_time }} - {{ eventInstance.end_time }}</v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
    </template>
  </v-list>
</template>
<script>
import { eventInstanceRoute } from '@/router'

export default {
  name: 'EventList',
  props: {
    camp: { type: Function, required: true },
    eventInstances: { type: Array, required: true }
  },
  data () {
    return {
    }
  },
  computed: {
  },
  methods: {
    eventInstanceLink (eventInstance) {
      return eventInstanceRoute(this.camp(), eventInstance)
    }
  }
}
</script>
<style lang="scss">

</style>
