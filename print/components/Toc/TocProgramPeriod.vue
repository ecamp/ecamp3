<template>
  <li>
    {{ $tc('entity.period.name') }} {{ period.description }}

    <ul>
      <toc-program-day v-for="day in days" :key="day.id" :day="day" :index="index" />
    </ul>
  </li>
</template>

<script>
export default {
  name: 'TocProgramPeriod',
  props: {
    index: { type: Number, required: true },
    period: { type: Object, required: true },
  },
  data() {
    return {
      days: null,
    }
  },
  async fetch() {
    await Promise.all([
      this.period.days().$loadItems(),
      this.period.scheduleEntries().$loadItems(),
    ])

    this.days = this.period.days().items
  },
}
</script>
