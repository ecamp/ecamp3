<!--
Displays several tabs with details on a single camp.
-->

<template>
  <router-view v-if="camp" :camp="camp" />
</template>

<script>
import campFromRoute from '@/mixins/campFromRoute'

export default {
  name: 'CampDetails',
  mixins: [campFromRoute]
}
</script>

<style scoped>

</style>
