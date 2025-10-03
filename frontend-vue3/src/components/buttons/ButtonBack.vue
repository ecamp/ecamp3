<template>
  <v-btn
    v-bind="$attrs"
    :icon="!visibleLabel"
    :aria-label="$t('global.button.back')"
    @click="$router.go(-1)"
  >
    <v-icon :left="visibleLabel">mdi-arrow-left</v-icon>
    <template v-if="visibleLabel">
      {{ $t('global.button.back') }}
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
