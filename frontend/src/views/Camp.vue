<!--
Displays several tabs with details on a single camp.
-->

<template>
  <router-view :camp-uri="this.$route.params.campUri" />
</template>

<script>
export default {
  name: 'CampDetails'
}
</script>

<style scoped>

</style>
