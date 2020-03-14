<template>
  <v-btn
    small
    color="error"
    v-on="$listeners">
    <v-icon :left="$vuetify.breakpoint.smAndUp" size="150%">mdi-delete</v-icon>
    <span class="d-none d-sm-block">
      <slot>Delete</slot>
    </span>
  </v-btn>
</template>

<script>
export default {
  name: 'ButtonDelete'
}
</script>

<style scoped>
</style>
