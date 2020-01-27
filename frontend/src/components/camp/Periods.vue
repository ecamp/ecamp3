<!--
Displays periods of a single camp.
-->

<template>
  <v-card>
    <div
      v-for="period in periods" :key="period.id">
      <v-list-item two-line>
        <v-list-item-content>
          <v-list-item-title class="headline">
            {{ period.description }}
          </v-list-item-title>
          <v-list-item-subtitle>{{ period.start }} - {{ period.end }}</v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
      <v-skeleton-loader
        v-if="events.loading"
        type="list-item-avatar-two-line@3" />
      <!-- wait for all events to be loaded => avoid each eventInstance to load separately -->
      <v-list v-if="!events.loading" dense>
        <v-list-item
          v-for="eventInstance in period.event_instances().items"
          :key="eventInstance._meta.self"
          two-line
          :to="{ name: 'event', params: { eventUri: eventInstance.event()._meta.self, dayUri: period.days().items[eventInstance.day_number]._meta.self } }">
          <v-chip class="mr-2" :color="eventInstance.event().event_category().color">{{ eventInstance.event().event_category().short }}</v-chip>
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
export default {
  name: 'Periods',
  props: {
    campUri: { type: String, required: true }
  },
  data () {
    return {
      editing: false,
      messages: []
    }
  },
  computed: {
    campDetails () {
      return this.api.get(this.campUri)
    },
    periods () {
      return this.campDetails.periods().items
    },
    organizationName () {
      return this.campDetails.camp_type().organization().name
    },
    buttonText () {
      return this.editing ? 'Speichern' : 'Bearbeiten'
    },
    events () {
      return this.campDetails.events()
    }
  },

  created: function () {
    // force reloading of all events
    if (this.campDetails.events()._meta.self) {
      this.api.reload(this.campDetails.events()._meta.self)
    }
  }
}
</script>
