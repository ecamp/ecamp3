<template>
  <div>
    <e-text-field
      v-model="activityCategory.short"
      :name="$tc('entity.activityCategory.fields.short')"
      vee-rules="required" />

    <e-text-field
      v-model="activityCategory.name"
      :name="$tc('entity.activityCategory.fields.name')"
      vee-rules="required" />

    <e-select
      v-model="activityCategory.activityTypeId"
      :items="activityTypeOptions"
      :name="$tc('entity.activityCategory.fields.activityType')"
      vee-rules="required" />

    <e-color-picker
      v-model="activityCategory.color"
      :name="$tc('entity.activityCategory.fields.color')"
      vee-rules="required" />

    <e-select
      v-model="activityCategory.numberingStyle"
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
  data: () => ({
    updateColorAndNumberingStyle: true
  }),
  computed: {
    activityTypes () {
      return this.camp.campType().activityTypes().items
    },
    activityTypeOptions () {
      return this.activityTypes.map(ct => ({
        value: ct.id,
        text: this.$tc(ct.name),
        object: ct
      }))
    },
    numberingStyles () {
      return ['1', 'a', 'A', 'i', 'I'].map(i => ({
        value: i,
        text: this.$tc('entity.activityCategory.numberingStyles.' + i)
      }))
    }
  },
  watch: {
    'activityCategory.activityTypeId': function (activityTypeId) {
      if (this.isNew && this.updateColorAndNumberingStyle) {
        this.updateColorAndNumberingStyle = false
        const activityType = this.activityTypes.filter(a => a.id === activityTypeId)[0]
        this.activityCategory.color = activityType.defaultColor
        this.activityCategory.numberingStyle = activityType.defaultNumberingStyle
      }
    }
  }
}
</script>
