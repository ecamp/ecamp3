<template>
  <div>
    <e-text-field
      v-model="localCategory.short"
      :name="$tc('entity.category.fields.short')"
      vee-rules="required"
    />

    <e-text-field
      v-model="localCategory.name"
      :name="$tc('entity.category.fields.name')"
      vee-rules="required"
    />

    <e-color-picker
      v-model="localCategory.color"
      :name="$tc('entity.category.fields.color')"
      vee-rules="required"
    />

    <e-select
      v-model="localCategory.numberingStyle"
      :items="numberingStyles"
      :name="$tc('entity.category.fields.numberingStyle')"
      vee-rules="required"
    />

    <e-select
      v-model="localCategory.preferredContentTypes"
      :items="contentTypes"
      :disabled="contentTypesLoading"
      :loading="contentTypesLoading"
      :name="$tc('entity.contentType.name', 2)"
      multiple
    />
  </div>
</template>

<script>
import { camelCase } from 'lodash'
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
      return ['1', 'a', 'A', 'i', 'I'].map((i) => ({
        value: i,
        text: this.$tc('entity.category.numberingStyles.' + i),
      }))
    },
    contentTypes() {
      if (this.contentTypesLoading) {
        return []
      }
      return this.api
        .get()
        .contentTypes()
        .items.map((ct) => ({
          value: ct._meta.self,
          text: this.$tc('contentNode.' + camelCase(ct.name) + '.name'),
        }))
        .sort(function (a, b) {
          return a.text.localeCompare(b.text)
        })
    },
    contentTypesLoading() {
      return this.api.get().contentTypes()._meta.loading
    },
  },
}
</script>
