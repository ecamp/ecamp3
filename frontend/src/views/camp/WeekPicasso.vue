<!--
Listing all event instances of a single period.
-->

<template>
  <content-card>
    <v-app-bar
      v-if="$vuetify.breakpoint.xs"
      dense fixed
      color="white"
      style="z-index: 300"
      :tile="false" class="ma-2 px-3">
      <v-btn v-if="searchOpen" icon @click="searchOpen = false">
        <v-icon>mdi-chevron-left</v-icon>
      </v-btn>
      <v-text-field
        hide-details
        :label="searchOpen ? 'Suchen' : 'Events & Camps suchen'"
        single-line
        @click="searchOpen = !searchOpen" />
      <router-link :to="{name: 'profile'}">
        <v-avatar size="32" class="ml-4">
          <img
            alt="Avatar"
            src="https://avatars0.githubusercontent.com/u/9064066?v=4&s=460">
        </v-avatar>
      </router-link>
    </v-app-bar>
    <v-dialog
      v-model="searchOpen"
      style="z-index: 4"
      overlay-color="white" hide-overlay
      fullscreen transition="dialog-top-transition">
      <v-card>
        <v-sheet v-if="$vuetify.breakpoint.xs" height="70" />
        <v-card-text>
          <v-skeleton-loader class="mx-4" boilerplate type="list-item-two-line@3" />
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-sheet v-if="$vuetify.breakpoint.xs" height="56" />
    <v-sheet>
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
  components: { ContentCard, Picasso: () => import('@/components/camp/Picasso'), EventList: () => import('@/components/camp/EventList') },
  props: {
    camp: { type: Function, required: true }
  },
  data () {
    return {
      searchOpen: false,
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
