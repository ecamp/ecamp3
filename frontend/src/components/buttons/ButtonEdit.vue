<template>
  <v-btn class="px-3 px-sm-4" color="primary" v-bind="$attrs" v-on="$listeners">
    <v-icon :left="$vuetify.breakpoint.smAndUp" size="150%">{{ icon }}</v-icon>
    <span class="d-sr-only d-sm-block">
      <slot>{{ $tc('global.button.edit') }}</slot>
    </span>
  </v-btn>
</template>

<script>
export default {
  name: 'ButtonEdit',
  props: {
    icon: { type: String, default: 'mdi-pencil' },
  },
}
</script>

<style scoped></style>
