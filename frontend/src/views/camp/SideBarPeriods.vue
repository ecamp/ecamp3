<!--
Displays periods of a single camp.
-->

<template>
  <side-bar>
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
  </side-bar>
</template>
<script>
import SideBar from '@/components/base/SideBar'

export default {
  name: 'SideBarPeriods',
  components: { SideBar },
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
  }
}
</script>
