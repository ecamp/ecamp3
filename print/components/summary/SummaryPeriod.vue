<template>
  <div class="tw-break-after-page" :class="pageSize">
    <h1
      :id="`content_${index}_period_${period.id}`"
      class="tw-text-center tw-font-semibold tw-mb-6"
    >
      {{ $t('print.' + camelCase(type) + '.title') }}:
      {{ period.description }}
    </h1>

    <generic-error-message v-if="error" :error="error" />
    <summary-day
      v-for="day in data.days"
      :key="day._meta.self"
      :index="index"
      :day="day"
      :all-content-nodes="data.contentNodes"
      :content-type="contentType"
      :filter="filter"
    />
  </div>
</template>

<script setup>
import camelCase from 'lodash-es/camelCase.js'
import { filterMatchScheduleEntry } from '@/common/helpers/filterMatchScheduleEntry.js'
import SummaryDay from './SummaryDay.vue'

const props = defineProps({
  camp: { type: Object, required: true },
  period: {
    type: Object,
    required: true,
  },
  index: { type: Number, required: true },
  contentType: { type: String, default: 'Storycontext' },
  type: { type: String, default: 'Story' },
  filter: { type: Object, default: () => ({}) },
  pageSize: { type: String, default: 'a4' },
})

const { $api } = useNuxtApp()

const { data, error } = await useAsyncData(
  `SummaryPeriod-${props.index}-${props.period._meta.self}`,
  async () => {
    const contentType = (await $api.get().contentTypes().$loadItems()).items.find(
      (contentType) => contentType.name === props.contentType
    )

    const [contentNodes] = await Promise.all([
      $api
        .get()
        .contentNodes({
          period: props.period._meta.self,
          contentType: contentType._meta.self,
        })
        .$loadItems(),
      $api
        .get()
        .contentNodes({
          isRoot: 'true',
          period: props.period._meta.self,
        })
        .$loadItems(),
      props.period.days().$loadItems(),
      props.period.scheduleEntries().$loadItems(),
      props.period.camp().categories().$loadItems(),
    ])

    return {
      days: props.period.days().items.filter((day) => {
        return (
          props.period
            .scheduleEntries()
            .items.filter(
              (scheduleEntry) =>
                scheduleEntry.day()._meta.self === day._meta.self &&
                filterMatchScheduleEntry(scheduleEntry, props.filter)
            ).length > 0
        )
      }),
      contentNodes: contentNodes.items,
    }
  }
)
</script>
