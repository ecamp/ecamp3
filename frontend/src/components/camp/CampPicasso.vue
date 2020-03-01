<!--
Listing all event instances of a single period.
-->

<template>
  <v-card>
    <v-toolbar dense class="mb-3" color="blue-grey lighten-5">
      <v-icon left>
        mdi-account-group
      </v-icon>
      <v-toolbar-title class="overflow-visible">
        Picasso
      </v-toolbar-title>
      <v-tabs v-model="tab" right
              center-active background-color="blue-grey lighten-5">
        <v-tab v-for="period in periods.items"
               :key="period.id">
          {{ period.description }}
        </v-tab>
      </v-tabs>
    </v-toolbar>
    <v-tabs-items v-model="tab">
      <v-tab-item v-for="period in periods.items"
                  :key="period.id">
        <v-skeleton-loader v-if="events._meta.loading" class="ma-3"
                           type="table-thead,table-row@6" />
        <picasso v-else
                 :camp="camp"
                 :event-instances="period.event_instances().items"
                 :start="new Date(Date.parse(period.start))"
                 :end="new Date(Date.parse(period.end))" />
      </v-tab-item>
    </v-tabs-items>
  </v-card>
</template>
<script>
import Picasso from '@/components/Picasso'

export default {
  name: 'CampPicassso',
  components: { Picasso },
  props: {
    camp: { type: Function, required: true }
  },
  data () {
    return {
      tab: null
    }
  },
  computed: {
    periods () {
      return this.camp().periods()
    },
    events () {
      return this.camp().events()
    }
  }
}
</script>
