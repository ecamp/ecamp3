<!--
Displays periods of a single camp.
-->

<template>
  <v-card>
    <v-subheader class="text-uppercase subtitle-2">
      Periods
    </v-subheader>
    <v-list>
      <v-list-item
        v-for="period in periods.items"
        :key="period._meta.self"
        two-line>
        <v-list-item-content>
          <v-list-item-title>{{ period.description }}</v-list-item-title>
          <v-list-item-subtitle>{{ period.start }} - {{ period.end }}</v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
    </v-list>
  </v-card>
</template>
<script>
import { eventInstanceRoute } from '@/router'

export default {
  name: 'CampPeriods',
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
    eventInstanceRoute (eventInstance) {
      return eventInstanceRoute(this.camp(), eventInstance)
    }
  }
}
</script>
