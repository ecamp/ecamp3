<template>
  <div>
    <e-text-field
      v-model="localActivityCategory.short"
      :name="$tc('entity.activityCategory.fields.short')"
      vee-rules="required" />

    <e-text-field
      v-model="localActivityCategory.name"
      :name="$tc('entity.activityCategory.fields.name')"
      vee-rules="required" />

    <e-color-picker
      v-model="localActivityCategory.color"
      :name="$tc('entity.activityCategory.fields.color')"
      vee-rules="required" />

    <e-select
      v-model="localActivityCategory.numberingStyle"
      :items="numberingStyles"
      :name="$tc('entity.activityCategory.fields.numberingStyle')"
      vee-rules="required" />
  </div>
</template>

<script>
export default {
  name: 'DialogActivityCategoryForm',
  props: {
    camp: { type: Object, required: true },
    isNew: { type: Boolean, required: true },
    activityCategory: { type: Object, required: true }
  },
  data () {
    return {
      updateColorAndNumberingStyle: true,
      localActivityCategory: this.activityCategory
    }
  },
  computed: {
    numberingStyles () {
      return ['1', 'a', 'A', 'i', 'I'].map(i => ({
        value: i,
        text: this.$tc('entity.activityCategory.numberingStyles.' + i)
      }))
    }
  }
}
</script>
