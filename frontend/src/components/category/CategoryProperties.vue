<template>
  <div>
    <api-text-field
      :uri="category._meta.self"
      fieldname="short"
      :name="$tc('entity.category.fields.short')"
      vee-rules="required"
    />

    <api-text-field
      :uri="category._meta.self"
      fieldname="name"
      :name="$tc('entity.category.fields.name')"
      vee-rules="required"
    />

    <api-color-picker
      :uri="category._meta.self"
      fieldname="color"
      :name="$tc('entity.category.fields.color')"
      vee-rules="required"
    />

    <api-select
      :uri="category._meta.self"
      fieldname="numberingStyle"
      :items="numberingStyles"
      :name="$tc('entity.category.fields.numberingStyle')"
      vee-rules="required"
    />
  </div>
</template>

<script>
export default {
  name: 'CategoryProperties',
  props: {
    category: { type: Object, required: true },
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
