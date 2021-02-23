<template>
  <div>
    <e-text-field
      v-model="localCategory.short"
      :name="$tc('entity.category.fields.short')"
      vee-rules="required" />

    <e-text-field
      v-model="localCategory.name"
      :name="$tc('entity.category.fields.name')"
      vee-rules="required" />

    <e-color-picker
      v-model="localCategory.color"
      :name="$tc('entity.category.fields.color')"
      vee-rules="required" />

    <e-select
      v-model="localCategory.numberingStyle"
      :items="numberingStyles"
      :name="$tc('entity.category.fields.numberingStyle')"
      vee-rules="required" />
  </div>
</template>

<script>
export default {
  name: 'DialogCategoryForm',
  props: {
    camp: { type: Object, required: true },
    isNew: { type: Boolean, required: true },
    category: { type: Object, required: true }
  },
  data () {
    return {
      updateColorAndNumberingStyle: true,
      localCategory: this.category
    }
  },
  computed: {
    numberingStyles () {
      return ['1', 'a', 'A', 'i', 'I'].map(i => ({
        value: i,
        text: this.$tc('entity.category.numberingStyles.' + i)
      }))
    }
  }
}
</script>
