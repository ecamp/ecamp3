<template>
  <v-bottom-navigation v-if="$vuetify.breakpoint.xs" app
                       fixed grow>
    <v-btn>
      <span>Material</span>
      <v-icon>mdi-package-variant</v-icon>
    </v-btn>
    <v-btn>
      <span>Tasks</span>
      <v-icon>mdi-format-list-checks</v-icon>
    </v-btn>
    <v-btn :to="{name: 'camp/overview'}">
      <span>Camp</span>
      <v-icon large>mdi-tent</v-icon>
    </v-btn>
    <v-btn :to="{name: 'camp/collaborators'}" exact>
      <span>Team</span>
      <v-icon>mdi-account-group</v-icon>
    </v-btn>
    <v-btn :to="{name: 'camp/admin'}">
      <span>Admin</span>
      <v-icon>mdi-cogs</v-icon>
    </v-btn>
  </v-bottom-navigation>
</template>

<script>
export default {
  name: 'NavMobile'
}
</script>

<style scoped>

</style>
