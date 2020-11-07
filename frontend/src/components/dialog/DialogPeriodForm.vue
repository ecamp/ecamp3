<template>
  <div>
    <e-text-field
      v-model="period.description"
      :name="$tc('entity.period.fields.description')"
      vee-rules="required" />

    <e-date-picker
      v-model="period.start"
      :name="$tc('entity.period.fields.start')"
      vee-id="start"
      :value-format="$moment.ISO_8601"
      vee-rules="required" />

    <e-date-picker
      v-model="period.end"
      :value-format="$moment.ISO_8601"
      :name="$tc('entity.period.fields.end')"
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
