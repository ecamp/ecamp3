<template>
  <v-btn
    small
    color="error"
    v-on="$listeners">
    <span class="d-none d-sm-block">
      <i class="v-icon v-icon--left mdi mdi-delete" />
      <slot>Delete</slot>
    </span>
    <span class="d-sm-none">
      <i class="v-icon mdi mdi-delete" />
    </span>
  </v-btn>
</template>

<script>
export default {
  name: 'ButtonDelete',
  data () {
    return {
    }
  }
}
</script>

<style scoped>
</style>
