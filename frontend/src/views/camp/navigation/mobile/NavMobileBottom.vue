<template>
  <v-bottom-navigation grow app background-color="blue-grey darken-4" dark>
    <v-btn :to="campRoute(camp(), 'program')">
      <span>{{ $tc('views.camp.navigation.mobile.navMobileBottom.program') }}</span>
      <v-icon>mdi-view-dashboard</v-icon>
    </v-btn>
    <v-btn :to="campRoute(camp(), 'story')">
      <span>{{ $tc('views.camp.navigation.mobile.navMobileBottom.story') }}</span>
      <v-icon>mdi-book-open-variant</v-icon>
    </v-btn>
    <v-btn :to="campRoute(camp(), 'dashboard')">
      <span>{{ camp().name }}</span>
      <v-icon large>mdi-tent</v-icon>
    </v-btn>
    <v-btn :to="campRoute(camp(), 'material')">
      <span>{{ $tc('views.camp.navigation.mobile.navMobileBottom.material') }}</span>
      <v-icon>mdi-package-variant</v-icon>
    </v-btn>
    <v-btn @click="$emit('input', true)">
      <span>{{ $tc('views.camp.navigation.mobile.navMobileBottom.more') }}</span>
      <v-icon>mdi-menu</v-icon>
    </v-btn>
  </v-bottom-navigation>
</template>

<script>
import { campRoute } from '@/router'

export default {
  name: 'NavMobileBottom',
  props: {
    camp: { type: Function, required: true },
  },
  data() {
    return {
      value: false,
    }
  },
  computed: {
    user() {
      return this.$auth.user()
    },
  },
  methods: {
    campRoute,
  },
}
</script>

<style lang="scss" scoped>
.v-bottom-navigation--fixed {
  height: auto !important;
  min-height: 56px;
  padding-bottom: env(safe-area-inset-bottom);
}

.v-application .ec-close-drawer {
  background-color: #{map-get($blue-grey, 'lighten-5')};
  border-top: 1px solid #{map-get($blue-grey, 'lighten-4')};
}
</style>
