<template>
  <v-btn
    v-bind="$attrs"
    :icon="!visibleLabel"
    :aria-label="$t('global.button.back')"
    @click="goBack"
  >
    <v-icon :start="visibleLabel" icon="mdi-arrow-left" />
    <template v-if="visibleLabel">
      {{ $t('global.button.back') }}
    </template>
  </v-btn>
</template>

<script>
export default {
  name: 'ButtonBack',
  props: {
    to: {
      type: [String, Object],
      default: null,
    },
    visibleLabel: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    goBack() {
      if (this.to) {
        this.$router.push(this.to)
        return
      }

      this.$router.back()
    },
  },
}
</script>

<style scoped></style>
