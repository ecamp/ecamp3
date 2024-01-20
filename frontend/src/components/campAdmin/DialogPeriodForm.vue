<template>
  <e-form name="period">
    <e-text-field
      v-model="localPeriod.description"
      name="description"
      vee-rules="required"
    />

    <e-date-picker
      v-model="localPeriod.start"
      name="start"
      vee-id="start"
      vee-rules="required"
      :max="localPeriod.end"
      icon="mdi-calendar-start"
    />

    <e-date-picker
      v-model="localPeriod.end"
      name="end"
      vee-rules="required|greaterThanOrEqual_date:@start"
      :min="localPeriod.start"
      icon="mdi-calendar-end"
    />
  </e-form>
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
