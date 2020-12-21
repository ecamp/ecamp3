<template>
  <v-btn-toggle class="ec-viewmode-switcher ma-1 ma-sm-3" dense rounded>
    <v-btn :to="{ query: { ...$route.query, list: true } }" exact>
      <v-icon>mdi-format-list-numbered</v-icon>
    </v-btn>
    <v-btn :to="{ query: { ...Array.from($route.query).map(({list, ...rest }) => rest ) } }" exact>
      <v-icon>mdi-calendar-month</v-icon>
    </v-btn>
  </v-btn-toggle>
</template>

<script>
export default {
  name: 'CampProgramViewModeSwitcher'
}
</script>

<style lang="scss" scoped>

.ec-viewmode-switcher {
  opacity: .6;
}

.ec-viewmode-switcher:hover {
  opacity: 1;
}

.ec-viewmode-switcher ::v-deep .v-btn {
  min-width: 48px;
}

@media #{map-get($display-breakpoints, 'xs-only')}{
  .ec-viewmode-switcher {
    position: fixed;
    z-index: 10;
    right: 0;
    top: 2px;
  }
}

@media #{map-get($display-breakpoints, 'sm-and-up')}{
  .ec-viewmode-switcher {
    position: fixed;
    z-index: 10;
    right: 8px;
    top: 73px !important;
  }
}
</style>
