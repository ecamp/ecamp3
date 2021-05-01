<!--
Admin screen of a camp: Displays details & periods of a single camp and allows to edit them.
-->

<template>
  <div>
    print Period
    {{ period().id }}
  </div>
</template>

<script>

export default {
  name: 'PrintPeriod',
  components: {
  },
  props: {
    period: { type: Function, required: true }
  },
  data () {
    return {}
  }
}
</script>

<style scoped>
</style>
