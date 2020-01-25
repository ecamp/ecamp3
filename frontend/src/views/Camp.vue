<!--
Displays several tabs with details on a single camp.
-->

<template>
  <v-card>
    <v-tabs>
      <v-tab
        :to="{ name: 'camp' }"
        exact>
        Info
      </v-tab>
      <v-tab :to="{ name: 'camp/collaborators' }">
        Collaborators
      </v-tab>
      <v-tab :to="{ name: 'camp/periods' }">
        Periods
      </v-tab>
      <v-tab :to="{ name: 'camp/picasso' }">
        Picasso
      </v-tab>
    </v-tabs>

    <div class="pa-4">
      <router-view :camp-uri="this.$route.params.campUri" />
    </div>
  </v-card>
</template>

<script>
export default {
  name: 'CampDetails'
}
</script>

<style scoped>

</style>
