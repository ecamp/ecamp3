<template>
  <v-bottom-navigation v-if="$vuetify.breakpoint.xs" app
                       fixed grow>
    <v-btn :to="{name: 'camps'}">
      <span>Meine Camps</span>
      <v-icon>mdi-format-list-bulleted-triangle</v-icon>
    </v-btn>
  </v-bottom-navigation>
</template>

<script>
export default {
  name: 'BottomBar'
}
</script>

<style scoped>

</style>
