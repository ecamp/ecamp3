<!--
Print preview to print a specific period
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
