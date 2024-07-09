<template>
  <div class="tw-break-after-page">
    <div>
      <h1 class="tw-text-center tw-font-semibold tw-mb-6">
        {{ $t('print.program.title') }}: {{ period.description }}
      </h1>
    </div>
    <generic-error-message v-if="error" :error="error" />
    <program-day
      v-for="day in days"
      v-else
      :key="'day' + day.id"
      :day="day"
      :show-daily-summary="showDailySummary"
      :show-activities="showActivities"
      :index="index"
    />
  </div>
</template>

<script setup>
const props = defineProps({
  period: { type: Object, required: true },
  showDailySummary: { type: Boolean, required: true },
  showActivities: { type: Boolean, required: true },
  index: { type: Number, required: true },
})

const { data: days, error } = await useAsyncData(
  `ProgramPeriod-${props.period._meta.self}`,
  async () => {
    await Promise.all([
      props.period.days().$loadItems(),
      props.period.scheduleEntries().$loadItems(),
      props.period.contentNodes().$loadItems(),
    ])

    return props.period.days().items
  }
)
</script>
