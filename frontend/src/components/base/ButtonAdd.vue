<template>
  <v-btn
    small
    color="success"
    v-on="$listeners">
    <v-icon :left="$vuetify.breakpoint.smAndUp" size="150%">mdi-plus</v-icon>
    <span class="d-none d-sm-block">
      <slot>Add</slot>
    </span>
  </v-btn>
</template>

<script>
export default {
  name: 'ButtonAdd'
}
</script>

<style scoped>
</style>
