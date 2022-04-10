<template>
  <li>
    {{ $tc('entity.period.name') }} {{ period.description }}

    <ul>
      <toc-program-day
        v-for="day in days"
        :key="day.id"
        :day="day"
        :index="index"
      />
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
    this.days = (await this.period.days()._meta.load).items
  },
}
</script>
