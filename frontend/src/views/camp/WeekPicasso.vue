<!--
Listing all event instances of a single period.
-->

<template>
  <content-card>
    <v-sheet>
      <mobile-search />
      <v-btn
        :fixed="$vuetify.breakpoint.xs"
        :absolute="!$vuetify.breakpoint.xs"
        light class="fab--top_nav"
        fab small
        style="z-index: 3"
        top right
        :class="{'float-right':!$vuetify.breakpoint.xs}"
        color="white"
        @click="picassoFormat = !picassoFormat">
        <v-icon v-if="picassoFormat">mdi-menu</v-icon>
        <v-icon v-else>mdi-calendar-month</v-icon>
      </v-btn>
      <picasso
        v-if="picassoFormat"
        :camp="camp"
        class="mx-2 ma-sm-0 pa-sm-2"
        :event-instances="firstPeriod.event_instances().items"
        :start="new Date(Date.parse(firstPeriod.start))"
        :end="new Date(Date.parse(firstPeriod.end))" />
      <event-list v-else
                  :camp="camp" :event-instances="firstPeriod.event_instances().items" />
      <v-btn
        :fixed="$vuetify.breakpoint.xs"
        :absolute="!$vuetify.breakpoint.xs"
        dark
        fab
        style="z-index: 3"
        bottom
        right
        class="fab--bottom_nav float-right"
        color="red">
        <v-icon>mdi-plus</v-icon>
      </v-btn>
    </v-sheet>
  </content-card>
</template>
<script>
import ContentCard from '@/components/base/ContentCard'

export default {
  name: 'WeekPicasso',
  components: {
    ContentCard,
    MobileSearch: () => import('@/components/base/MobileSearch'),
    Picasso: () => import('@/components/camp/Picasso'),
    EventList: () => import('@/components/camp/EventList')
  },
  props: {
    camp: { type: Function, required: true }
  },
  data () {
    return {
      picassoFormat: true,
      tab: null
    }
  },
  computed: {
    periods () {
      return this.camp().periods()
    },
    firstPeriod () {
      return this.periods.items[0]
    }
  },
  mounted () {
    this.camp().events()
  }
}
</script>

<style lang="scss">
  .fab--bottom_nav {
    position: fixed;
    bottom: 16px + 56px !important;
    @media #{map-get($display-breakpoints, 'sm-and-up')}{
      bottom: 16px + 36px !important;
    }
  }

  .fab--top_nav {
    position: fixed;
    top: 16px + 105px !important;
    @media #{map-get($display-breakpoints, 'sm-and-up')}{
      top: 16px + 65px !important;
    }
  }
</style>
