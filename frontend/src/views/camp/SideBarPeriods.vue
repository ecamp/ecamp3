<!--
Displays periods of a single camp.
-->

<template>
  <side-bar>
    <content-card>
      <v-subheader class="text-uppercase subtitle-2">
        Periods
      </v-subheader>
      <v-list>
        <v-list-item
          v-for="listperiod in periods.items"
          :key="listperiod._meta.self"
          two-line>
          <v-list-item-content>
            <v-list-item-title>{{ listperiod.description }}</v-list-item-title>
            <v-list-item-subtitle>{{ listperiod.start }} - {{ listperiod.end }}</v-list-item-subtitle>
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
    camp: { type: Function, required: true },
    period: { type: Function, required: true }
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
