<template>
  <v-bottom-navigation grow app bg-color="blue-grey-darken-4" dark class="ec-bottombar">
    <v-btn :to="campRoute(camp, 'program')">
      <v-icon>mdi-view-dashboard</v-icon>
      <span>{{ $t('views.camp.navigation.mobile.navBottombar.program') }}</span>
    </v-btn>
    <v-btn :to="campRoute(camp, 'story')">
      <v-icon>mdi-book-open-variant</v-icon>
      <span>{{ $t('views.camp.navigation.mobile.navBottombar.story') }}</span>
    </v-btn>
    <v-btn
      :to="campRoute(camp, 'dashboard')"
      class="ec-home-button flex-shrink-0 flex-grow-1 px-0"
    >
      <v-icon size="large" icon="mdi-tent" />
      <span class="ec-home-button__inner">{{ campShortTitle(camp) }}</span>
    </v-btn>
    <v-btn :to="materialListRoute(camp, '/lists')">
      <v-icon>mdi-package-variant</v-icon>
      <span>{{ $t('views.camp.navigation.mobile.navBottombar.material') }}</span>
    </v-btn>
    <v-btn @click="$emit('input', true)">
      <v-icon>mdi-menu</v-icon>
      <span>{{ $t('views.camp.navigation.mobile.navBottombar.more') }}</span>
    </v-btn>
  </v-bottom-navigation>
</template>

<script>
import { campRoute, materialListRoute } from '@/router'
import campShortTitle from '@/helpers/campShortTitle.js'

export default {
  name: 'NavBottombar',
  props: {
    camp: { type: Object, required: true },
  },
  emits: ['input'],
  data() {
    return {
      value: false,
    }
  },
  methods: {
    materialListRoute,
    campRoute,
    campShortTitle,
  },
}
</script>

<style lang="scss" scoped>
@use 'vuetify/settings';
@use 'sass:map';

.ec-bottombar {
  height: auto !important;
  min-height: 56px;
  padding-bottom: env(safe-area-inset-bottom);
}

:deep(.v-btn--stacked .v-icon) {
  --v-icon-size-multiplier: 1;
}

:deep(.v-bottom-navigation--grow .v-bottom-navigation__content > .v-btn) {
  flex-basis: auto;
  flex-grow: 0;
  flex-shrink: 1;
  width: 100%;
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
  text-wrap: balance;
  line-height: 1 !important;
  padding-bottom: 5px;
}
</style>
