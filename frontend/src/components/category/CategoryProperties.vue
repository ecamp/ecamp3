<template>
  <div>
    <api-text-field
      :uri="category._meta.self"
      path="short"
      :name="$tc('entity.category.fields.short')"
      :disabled="disabled"
      vee-rules="required"
    />

    <api-text-field
      :uri="category._meta.self"
      path="name"
      :name="$tc('entity.category.fields.name')"
      :disabled="disabled"
      vee-rules="required"
    />

    <api-color-picker
      :uri="category._meta.self"
      path="color"
      :name="$tc('entity.category.fields.color')"
      :disabled="disabled"
      vee-rules="required"
    />

    <api-select
      :uri="category._meta.self"
      path="numberingStyle"
      :items="numberingStyles"
      :name="$tc('entity.category.fields.numberingStyle')"
      :disabled="disabled"
      vee-rules="required"
    />
  </div>
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
      return ['1', 'a', 'A', 'i', 'I'].map((i) => ({
        value: i,
        text: this.$tc('entity.category.numberingStyles.' + i),
      }))
    },
  },
}
</script>

<style scoped>
div {
  max-width: 600px;
}
</style>
