<template>
  <v-btn
    small
    color="primary"
    v-on="$listeners">
    <span class="d-none d-sm-block">
      <i class="v-icon v-icon--left mdi mdi-pencil" />
      <slot>Edit</slot>
    </span>
    <span class="d-sm-none">
      <i class="v-icon mdi mdi-pencil" />
    </span>
  </v-btn>
</template>

<script>
export default {
  name: 'ButtonEdit',
  data () {
    return {
    }
  }
}
</script>

<style scoped>
</style>
