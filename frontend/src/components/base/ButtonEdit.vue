<template>
  <v-btn
    small
    color="primary"
    v-on="$listeners">
    <v-icon :left="$vuetify.breakpoint.smAndUp" size="150%">mdi-pencil</v-icon>
    <span class="d-none d-sm-block">
      <slot>Edit</slot>
    </span>
  </v-btn>
</template>

<script>
export default {
  name: 'ButtonEdit'
}
</script>

<style scoped>
</style>
