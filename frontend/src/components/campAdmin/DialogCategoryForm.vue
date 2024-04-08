<template>
  <e-form name="category">
    <div class="e-form-container d-flex gap-2">
      <e-text-field
        v-model="localCategory.short"
        path="short"
        vee-rules="required"
        class="flex-grow-1"
      />
      <slot name="textFieldTitleAppend" />
    </div>

    <e-text-field v-model="localCategory.name" path="name" vee-rules="required" />

    <e-color-picker v-model="localCategory.color" path="color" vee-rules="required" />

    <e-select
      v-model="localCategory.numberingStyle"
      :items="numberingStyles"
      path="numberingStyle"
      vee-rules="required"
    />
  </e-form>
</template>

<script>
import ESelect from '../form/base/ESelect.vue'

export default {
  name: 'DialogCategoryForm',
  components: { ESelect },
  props: {
    camp: { type: Object, required: true },
    isNew: { type: Boolean, required: true },
    category: { type: Object, required: true },
  },
  data() {
    return {
      updateColorAndNumberingStyle: true,
      localCategory: this.category,
    }
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
