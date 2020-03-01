<!--
Home screen of a camp: Displays details & perdios of a single camp and allows to edit them.
-->

<template>
  <div>
    <camp-settings :camp="camp" />

    <camp-periods :camp="camp" />
  </div>
</template>

<script>
import CampSettings from '@/components/camp/CampSettings'
import CampPeriods from '@/components/camp/CampPeriods'

export default {
  name: 'CampHome',
  components: { CampSettings, CampPeriods },
  props: {
    camp: { type: Function, required: true }
  },
  data () {
    return {
    }
  }
}
</script>

<style scoped>
</style>
