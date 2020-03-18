<!--
Displays several tabs with details on a single camp.
-->

<template>
  <v-container fluid>
    <router-view :camp="camp" />
  </v-container>
</template>

<script>
export default {
  name: 'Camp',
  props: {
    camp: { type: Function, required: true }
  }
}
</script>

<style scoped>

</style>
