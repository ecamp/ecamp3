<template>
  <api-form :entity="category" name="category">
    <api-text-field path="short" :disabled="disabled" vee-rules="required" />

    <api-text-field path="name" :disabled="disabled" vee-rules="required" />

    <api-color-picker path="color" :disabled="disabled" vee-rules="required" />

    <api-select
      path="numberingStyle"
      :items="numberingStyles"
      :disabled="disabled"
      vee-rules="required"
    />
  </api-form>
</template>

<script>
export default {
  name: 'CategoryProperties',
  props: {
    category: { type: Object, required: true },
    disabled: { type: Boolean, default: false },
  },
  computed: {
    numberingStyles() {
      return ['1', 'a', 'A', 'i', 'I', '-'].map((i) => ({
        value: i,
        text: this.$tc('entity.category.numberingStyles.' + i),
      }))
    },
  },
}
</script>

<style scoped>
/* makes the input fields smaller */
/* eslint-disable-next-line vue-scoped-css/no-unused-selector */
div {
  max-width: 600px;
}
</style>
