<!--
Displays periods of a single camp.
-->

<template>
  <side-bar>
    <content-card>
      <v-subheader class="text-uppercase subtitle-2">
        {{ $tc('views.camp.sideBarPeriods.title') }}
      </v-subheader>
      <v-list>
        <v-list-item
          v-for="item in periods.items"
          :key="item._meta.self"
          :to="periodRoute(item)"
          two-line>
          <v-list-item-content>
            <v-list-item-title>{{ item.description }}</v-list-item-title>
              <v-list-item-subtitle>
                {{ $moment.utc(period.start).format($tc('global.moment.dateShort')) }} - {{ $moment.utc(period.end).format($tc('global.moment.dateShort')) }}
              </v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </content-card>
  </side-bar>
</template>
<script>
import SideBar from '@/components/navigation/SideBar'
import ContentCard from '@/components/layout/ContentCard'
import { periodRoute } from '@/router'

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
  },
  methods: {
    periodRoute
  }
}
</script>
