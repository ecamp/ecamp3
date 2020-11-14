<!--
Admin screen of a camp: Displays details & periods of a single camp and allows to edit them.
-->

<template>
  <content-card title="Material">
    <v-card-text>
      <material-lists :camp="camp" />
    </v-card-text>
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard'
import MaterialLists from '@/components/camp/MaterialLists'

export default {
  name: 'Material',
  components: {
    ContentCard,
    MaterialLists
  },
  props: {
    camp: { type: Function, required: true }
  },
  data () {
    return {}
  }
}
</script>

<style scoped>
</style>
