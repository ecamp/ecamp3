<template>
  <v-btn
    :icon="!visibleLabel"
    :aria-label="$tc('global.button.back')"
    v-bind="$attrs"
    @click="$router.go(-1)"
  >
    <v-icon :left="visibleLabel">mdi-arrow-left</v-icon>
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
