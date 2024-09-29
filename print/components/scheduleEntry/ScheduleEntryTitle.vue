<template>
  <generic-error-message v-if="error" :error="error" />
  <div
    v-else
    :id="`scheduleEntry_${scheduleEntry.id}`"
    class="schedule-entry-title tw-break-after-avoid tw-flex tw-items-baseline tw-justify-between tw-gap-2 tw-border-b-4 tw-border-b-black tw-pb-2"
    :style="{ borderColor: scheduleEntry.activity().category().color }"
  >
    <h2
      :id="`content_${index}_scheduleEntry_${scheduleEntry.id}`"
      class="tw-text-3xl tw-font-semibold tw-pt-1 flex gap-3"
    >
      <category-label :category="scheduleEntry.activity().category()" />
      <span class="tw-tabular-nums">
        {{ scheduleEntry.number }}
      </span>
      <span>
        {{ scheduleEntry.activity().title }}
      </span>
    </h2>
    <div class="tw-text-lg">
      {{ rangeShort(scheduleEntry.start, scheduleEntry.end) }}
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  scheduleEntry: { type: Object, required: true },
  index: { type: Number, required: true },
})

const { error } = await useAsyncData(
  `ScheduleEntryTitle-${props.scheduleEntry._meta.self}`,
  async () => {
    return await Promise.all([
      props.scheduleEntry._meta.load,
      props.scheduleEntry.activity()._meta.load,
      props.scheduleEntry.activity().category()._meta.load,
    ])
  }
)
</script>

<script>
import CategoryLabel from '../generic/CategoryLabel.vue'
import { dateHelperUTCFormatted } from '@/mixins/dateHelperUTCFormatted.js'

export default defineNuxtComponent({
  components: { CategoryLabel },
  mixins: [dateHelperUTCFormatted],
})
</script>

<style lang="scss" scoped></style>
