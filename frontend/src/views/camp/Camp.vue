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
  },
  mounted () {
    this.camp()._meta.load.then(camp => {
      // Loading camp successful
      //  -> Set LastCampId
      this.api.patch(this.api.get().profile(), {
        lastCampId: camp.id
      })
    }, () => {
      // Loading camp failed
      //  -> reset lastCampId
      this.api.patch(this.api.get().profile(), {
        lastCampId: ''
      })
      //  -> GoTo Camp-Overview
      this.$router.push({ name: 'camps' })
    })
  }
}
</script>

<style scoped>

</style>
