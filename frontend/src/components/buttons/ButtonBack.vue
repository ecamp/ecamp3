<template>
  <v-btn
    v-bind="$attrs"
    :icon="!visibleLabel"
    :aria-label="$tc('global.button.back')"
    @click="$router.go(-1)"
  >
    <v-icon :start="visibleLabel">mdi-arrow-left</v-icon>
    <template v-if="visibleLabel">
      {{ $tc('global.button.back') }}
    </template>
  </v-btn>
</template>

<script>
export default {
  name: 'ButtonBack',
  props: {
    visibleLabel: {
      type: Boolean,
      default: false,
    },
  },
}
</script>

<style scoped></style>
