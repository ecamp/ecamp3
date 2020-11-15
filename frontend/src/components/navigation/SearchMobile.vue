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
        hide-details
        :label="searchOpen ? $tc('components.navigation.searchMobile.search') : $tc('components.navigation.searchMobile.searchActivitiesAndCamps')"
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
        <v-sheet height="70" />
        <v-card-text>
          <v-skeleton-loader class="mx-4" boilerplate type="list-item-two-line@3" />
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-sheet>
</template>

<script>
export default {
  name: 'SearchMobile',
  data () {
    return {
      searchOpen: false
    }
  }
}
</script>

<style scoped>

</style>
