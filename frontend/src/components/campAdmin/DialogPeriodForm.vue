<template>
  <div>
    <e-text-field
      v-model="localPeriod.description"
      :name="$tc('entity.period.fields.description')"
      vee-rules="required"
    />

    <slot name="beforeDates" />
    <e-date-picker
      v-model="localPeriod.start"
      :disabled="startDisabled"
      :name="$tc('entity.period.fields.start')"
      vee-id="start"
      vee-rules="required"
    />

    <e-date-picker
      v-model="localPeriod.end"
      :disabled="endDisabled"
      :name="$tc('entity.period.fields.end')"
      vee-rules="required|greaterThanOrEqual_date:@start"
    />
  </div>
</template>

<script>
export default {
  name: 'DialogPeriodForm',
  props: {
    period: { type: Object, required: true },
    startDisabled: { type: Boolean, required: false, default: false },
    endDisabled: { type: Boolean, required: false, default: false },
  },
  data() {
    return {
      localPeriod: this.period,
    }
  },
}
</script>
