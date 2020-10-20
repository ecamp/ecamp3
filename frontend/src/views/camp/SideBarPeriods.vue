<!--
Displays periods of a single camp.
-->

<template>
  <side-bar>
    <content-card>
      <v-subheader class="text-uppercase subtitle-2">
        {{ $tc('components.camp.campPeriods.title') }}
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
    </content-card>
  </side-bar>
</template>
<script>
import SideBar from '@/components/navigation/SideBar'
import ContentCard from '@/components/layout/ContentCard'

export default {
  name: 'SideBarPeriods',
  components: { ContentCard, SideBar },
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
    activities () {
      return this.camp().activities()
    }
  }
}
</script>
