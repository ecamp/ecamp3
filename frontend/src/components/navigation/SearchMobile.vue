<template>
  <v-sheet min-height="56">
    <v-app-bar
      dense fixed
      color="white"
      style="z-index: 300"
      :tile="false" class="ma-2 px-3">
      <v-btn v-if="searchOpen" icon @click="searchOpen = false">
        <v-icon>mdi-chevron-left</v-icon>
      </v-btn>
      <v-text-field
        v-model="filter"
        hide-details
        autocomplete="off"
        :label="searchOpen ? $tc('global.button.search') : $tc('components.navigation.searchMobile.searchActivitiesAndCamps')"
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
      class="ec-search-overlay"
      style="z-index: 4!important;"
      overlay-color="white" hide-overlay
      fullscreen transition="dialog-top-transition">
      <v-card>
        <v-sheet height="70" />
        <v-subheader>{{ $tc('entity.activity.name', 2) }}</v-subheader>
        <v-alert v-if="filter.length > 0 && filteredScheduleEntries.length < 1">
          {{ $tc('global.empty.resultFor', 10, { search: $tc('entity.activity.name', 1) }) }}
        </v-alert>
        <activity-list :schedule-entries="filteredScheduleEntries" :period="period"
                       class="py-0" />
        <v-subheader>{{ $tc('entity.camp.name', 2) }}</v-subheader>
        <v-alert v-if="filter.length > 0 && filteredCamps.length < 1">
          {{ $tc('global.empty.resultFor', 10, { search: $tc('entity.camp.name', 1) }) }}
        </v-alert>
        <v-container class="py-0">
          <v-row align-content="space-between">
            <v-col v-for="camp in filteredCamps" :key="camp.id"
                   :to="campRoute(camp)"
                   :ripple="false"
                   cols="4"
                   class="text-center flex flex-column"
                   @click="searchOpen = false">
              <v-avatar size="64" color="blue-grey lighten-3" class="mb-2">
                <v-icon dark size="32">$vuetify.icons.ecamp</v-icon>
              </v-avatar>
              <div class="caption">
                {{ camp.title }}
              </div>
            </v-col>
          </v-row>
        </v-container>
        <v-list-item :to="{name: 'camps'}" class="text-center">
          {{ $tc('components.navigation.searchMobile.allCamps') }}
        </v-list-item>
      </v-card>
    </v-dialog>
  </v-sheet>
</template>

<script>
import { campRoute } from '@/router'
import ActivityList from '@/components/camp/ActivityList'

const anyOf = function (terms) {
  if (!Array.isArray(terms)) {
    throw new Error('terms must be used with an array')
  }
  return {
    matches (filter) {
      return terms.map(term => term.toLowerCase()).some(term => term.includes(filter.toLowerCase()))
    }
  }
}

export default {
  name: 'SearchMobile',
  components: { ActivityList },
  props: {
    period: {
      type: Function,
      required: true
    }
  },
  data () {
    return {
      searchOpen: false,
      filter: ''
    }
  },
  computed: {
    camps () {
      return this.api.get().camps()
    },
    filteredCamps () {
      return this.filter
        ? this.camps.items
          .filter((camp) => anyOf([camp.title, camp.name, camp.motto]).matches(this.filter))
          .slice(0, 3)
        : this.camps.items.slice(0, 3)
    },
    scheduleEntries () {
      return this.period().scheduleEntries()
    },
    filteredScheduleEntries () {
      return this.filter
        ? this.scheduleEntries.items
          .filter((scheduleEntry) => anyOf([
            scheduleEntry.number,
            scheduleEntry.activity().title,
            scheduleEntry.activity().activityCategory().name,
            scheduleEntry.activity().activityCategory().short
          ]).matches(this.filter))
          .slice(0, 3)
        : this.scheduleEntries.items.slice(0, 3)
    }
  },
  methods: {
    campRoute
  }
}
</script>

<style scoped>
.ec-search-overlay {
  z-index: 120;
}
</style>
