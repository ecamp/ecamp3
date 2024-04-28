<template>
  <li>
    {{ $t('entity.period.name') }} {{ period.description }}

    <ul>
      <generic-error-message v-if="error" :error="error" />
      <toc-program-day
        v-for="day in days"
        v-else
        :key="day.id"
        :day="day"
        :index="index"
      />
    </ul>
  </li>
</template>

<script setup>
const props = defineProps({
  index: { type: Number, required: true },
  period: { type: Object, required: true },
})

const { data: days, error } = await useAsyncData(
  `TocProgramPeriod-${props.period._meta.self}`,
  async () => {
    await Promise.all([
      props.period.days().$loadItems(),
      props.period.scheduleEntries().$loadItems(),
    ])

    return props.period.days().items
  }
)
</script>
