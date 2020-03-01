<template>
  <span>

    <v-icon
      v-if="status === 'success'"
      color="green">
      mdi-check
    </v-icon>

    <v-progress-circular
      v-if="status ==='saving'"
      indeterminate
      color="primary"
      size="20"
      class="mr-2" />

  </span>
</template>

<script>
export default {
  name: 'StatusIcon',
  props: {
    status: {
      type: String,
      required: true,
      validator: function (value) {
      // The value must match one of these strings
        if (['init', 'success', 'saving'].indexOf(value) === -1) console.log(value)
        return ['init', 'success', 'saving'].indexOf(value) !== -1
      }
    }
  }
}
</script>

<style scoped>
</style>
