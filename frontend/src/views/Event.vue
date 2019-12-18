<!--
Displays a single event
-->

<template>
  <div>
    <h3>Event</h3>
    {{ this.$route.params.eventUri }}
  </div>
</template>

<script>
export default {
  name: 'Event'
}
</script>

<style scoped>

</style>
