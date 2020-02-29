<template>
  <v-btn
    small
    color="success"
    v-on="$listeners">
    <i class="v-icon v-icon--left mdi mdi-plus" />
    <slot>Add</slot>
  </v-btn>
</template>

<script>
export default {
  name: 'ButtonAdd',
  data () {
    return {
    }
  }
}
</script>

<style scoped>
</style>
