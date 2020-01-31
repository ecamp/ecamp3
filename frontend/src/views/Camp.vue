<!--
Displays several tabs with details on a single camp.
-->

<template>
  <router-view :camp="camp" />
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
