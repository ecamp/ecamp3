<template>
  <div>
    <e-text-field
      v-model="localPeriod.description"
      :name="$tc('entity.period.fields.description')"
      vee-rules="required"
    />

    <e-date-picker
      v-model="localPeriod.start"
      :name="$tc('entity.period.fields.start')"
      vee-id="start"
      vee-rules="required"
      :max="localPeriod.end"
    />

    <e-date-picker
      v-model="localPeriod.end"
      :name="$tc('entity.period.fields.end')"
      vee-rules="required|greaterThanOrEqual_date:@start"
      :min="localPeriod.start"
    />
  </div>
</template>

<script>
export default {
  name: 'DialogPeriodForm',
  props: {
    period: { type: Object, required: true },
  },
  data() {
    return {
      localPeriod: this.period,
    }
  },
}
</script>
