<!--
Displays periods of a single camp.
-->

<template>
  <v-card>
    <div
      v-for="period in periods.items" :key="period.id">
      <v-list-item two-line>
        <v-list-item-content>
          <v-list-item-title class="headline">
            {{ period.description }}
          </v-list-item-title>
          <v-list-item-subtitle>{{ period.start }} - {{ period.end }}</v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
      <v-skeleton-loader
        v-if="events._meta.loading"
        type="list-item-avatar-two-line@3" />
      <!-- wait for all events to be loaded => avoid each eventInstance to load separately -->
      <v-list v-else dense>
        <v-list-item
          v-for="eventInstance in period.event_instances().items"
          :key="eventInstance._meta.self"
          two-line
          :to="eventRoute(eventInstance)">
          <v-chip class="mr-2" :color="eventInstance.event().event_category().color.toString()">{{ eventInstance.event().event_category().short }}</v-chip>
          <v-list-item-content>
            <v-list-item-title>{{ eventInstance.event().title }}</v-list-item-title>
            <v-list-item-subtitle>{{ eventInstance.start_time }} - {{ eventInstance.end_time }}</v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </v-list>
      <v-divider />
    </div>
    <v-spacer />
  </v-card>
</template>
<script>
import { eventRoute } from '@/router'

export default {
  name: 'Periods',
  props: {
    camp: { type: Function, required: true }
  },
  data () {
    return {
      editing: false,
      messages: []
    }
  },
  computed: {
    periods () {
      return this.camp().periods()
    },
    events () {
      return this.camp().events()
    }
  },
  methods: {
    eventRoute (eventInstance) {
      return eventRoute(this.camp(), eventInstance)
    }
  }
}
</script>
