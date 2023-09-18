<template>
  <v-icon
    :class="{ 'v-icon--inherit': inherit, 'v-icon--spin': spin }"
    v-bind="$attrs"
    v-on="$listeners"
  >
    <slot />
  </v-icon>
</template>

<script>
export default {
  name: 'GenericIcon',
  props: {
    inherit: {
      type: Boolean,
      default: false,
    },
    spin: {
      type: Boolean,
      default: false,
    },
  },
}
</script>

<style scoped>
.v-icon--inherit {
  font-size: inherit !important;
}
.v-icon--spin {
  animation: spin-only 1s linear infinite;
}
</style>
