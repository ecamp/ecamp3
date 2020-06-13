<template>
  <div>
    <e-text-field
      v-model="activityCategory.short"
      :name="$t('activityCategory.short')"
      :label="$t('activityCategory.short')"
      vee-rules="required" />

    <e-text-field
      v-model="activityCategory.name"
      :name="$t('activityCategory.name')"
      :label="$t('activityCategory.name')"
      vee-rules="required" />

    <e-select
      v-model="activityCategory.activityTypeId"
      :items="activityTypes"
      :name="$t('activityCategory.activityType')"
      :label="$t('activityCategory.activityType')"
      vee-rules="required" />

    <e-color-picker
      v-model="activityCategory.color"
      :name="$t('activityCategory.color')"
      :label="$t('activityCategory.color')"
      vee-rules="required" />

    <e-select
      v-model="activityCategory.numberingStyle"
      :items="numberingStyles"
      :name="$t('activityCategory.numberingStyle')"
      :label="$t('activityCategory.numberingStyle')"
      vee-rules="required" />
  </div>
</template>

<script>
export default {
  name: 'DialogActivityCategoryForm',
  props: {
    activityCategory: { type: Object, required: true }
  },
  data: () => ({}),
  computed: {
    activityTypes () {
      return this.api.get().activityTypes().items.map(ct => ({
        value: ct.id,
        text: this.$i18n.t(ct.name),
        object: ct
      }))
    },
    numberingStyles () {
      return ['1', 'a', 'A', 'i', 'I'].map(i => ({
        value: i,
        text: this.$i18n.t('activityCategory.' + i)
      }))
    }
  },
  watch: {
    'activityCategory.activityTypeId': function (activityTypeId) {
      const activityType = this.api.get().activityTypes({ activityTypeId: activityTypeId })
      this.activityCategory.color = activityType.defaultColor
      this.activityCategory.numberingStyle = activityType.defaultNumberingStyle
    }
  }
}
</script>
