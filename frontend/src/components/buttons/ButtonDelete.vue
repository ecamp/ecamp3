<template>
  <v-btn
    class="px-3 px-sm-4"
    min-width="0"
    color="error"
    v-on="$listeners">
    <v-icon :left="$vuetify.breakpoint.smAndUp" size="150%">{{ icon }}</v-icon>
    <span class="d-none d-sm-block">
      <slot>Delete</slot>
    </span>
  </v-btn>
</template>

<script>
export default {
  name: 'ButtonDelete',
  props: {
    icon: { type: String, default: 'mdi-delete' }
  }
}
</script>

<style scoped>
</style>
