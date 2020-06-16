<template>
  <div>
    <e-text-field
      v-model="period.description"
      :name="$t('entity.period.fields.description')"
      vee-rules="required" />

    <e-date-picker
      v-model="period.start"
      :name="$t('entity.period.fields.start')"
      vee-id="start"
      vee-rules="required" />

    <e-date-picker
      v-model="period.end"
      :name="$t('entity.period.fields.end')"
      vee-rules="required|minDate:@start" />
  </div>
</template>

<script>
export default {
  name: 'DialogPeriodForm',
  props: {
    period: { type: Object, required: true }
  }
}
</script>
