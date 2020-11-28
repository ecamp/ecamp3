<template>
  <v-btn-toggle class="view_mode--switcher ma-3" dense rounded>
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

.view_mode--switcher {
  opacity: .6;
}

.view_mode--switcher:hover {
  opacity: 1;
}

@media #{map-get($display-breakpoints, 'xs-only')}{
  .view_mode--switcher {
    position: fixed;
    z-index: 10;
    right: 0;
    top: 0;
  }
}

@media #{map-get($display-breakpoints, 'sm-and-up')}{
  .view_mode--switcher {
    position: fixed;
    z-index: 10;
    right: 8px;
    top: 73px !important;
  }
}
</style>
