<template>
  <v-bottom-navigation grow app background-color="blue-grey darken-4" dark>
    <v-btn :to="campRoute(camp, 'program')">
      <span>{{ $tc('views.camp.navigation.mobile.navBottombar.program') }}</span>
      <v-icon>mdi-view-dashboard</v-icon>
    </v-btn>
    <v-btn :to="campRoute(camp, 'story')">
      <span>{{ $tc('views.camp.navigation.mobile.navBottombar.story') }}</span>
      <v-icon>mdi-book-open-variant</v-icon>
    </v-btn>
    <v-btn
      :to="campRoute(camp, 'dashboard')"
      class="ec-home-button flex-shrink-0 flex-grow-1 px-0"
    >
      <span class="ec-home-button__inner">{{ campShortTitle(camp) }}</span>
      <v-icon large>mdi-tent</v-icon>
    </v-btn>
    <v-btn :to="materialListRoute(camp, '/lists')">
      <span>{{ $tc('views.camp.navigation.mobile.navBottombar.material') }}</span>
      <v-icon>mdi-package-variant</v-icon>
    </v-btn>
    <v-btn @click="$emit('input', true)">
      <span>{{ $tc('views.camp.navigation.mobile.navBottombar.more') }}</span>
      <v-icon>mdi-menu</v-icon>
    </v-btn>
  </v-bottom-navigation>
</template>

<script>
import { campRoute, materialListRoute } from '@/router'
import { mapGetters } from 'vuex'
import campShortTitle from '@/common/helpers/campShortTitle.js'

export default {
  name: 'NavBottombar',
  props: {
    camp: { type: Object, required: true },
  },
  data() {
    return {
      value: false,
    }
  },
  computed: {
    ...mapGetters({
      user: 'getLoggedInUser',
    }),
  },
  methods: {
    materialListRoute,
    campRoute,
    campShortTitle,
  },
}
</script>

<style lang="scss" scoped>
// eslint-disable-next-line vue-scoped-css/no-unused-selector
.v-bottom-navigation--fixed {
  height: auto !important;
  min-height: 56px;
  padding-bottom: env(safe-area-inset-bottom);
}

.ec-home-button {
  flex-basis: 20% !important;
}
.ec-home-button :deep(.v-btn__content) {
  width: 100%;
}
.ec-home-button__inner {
  white-space: normal;
  width: 100%;
  word-break: break-word;
  text-align: center;
}
</style>
