<template>
  <v-chip
    label
    outlined
    :color="value ? 'primary' : null"
    @click="$emit('input', !value)"
    >{{ label }}</v-chip
  >
</template>

<script>
export default {
  name: 'BooleanFilter',
  props: {
    value: Boolean,
    label: { type: String, required: true },
  },
}
</script>

<style scoped></style>
