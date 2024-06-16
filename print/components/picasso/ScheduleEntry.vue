<template>
  <div>
    <div class="tw-float-left tw-font-weight-medium tw-tabular-nums tw-font-medium">
      <!-- link jumps to first instance of scheduleEntry within the document -->
      <a
        :href="`#scheduleEntry_${scheduleEntry.id}`"
        :style="{ color: getActivityTextColor(scheduleEntry) }"
      >
        {{ scheduleEntry.number }} {{ scheduleEntry.activity().category().short }}:
        {{ scheduleEntry.activity().title }}
      </a>
    </div>
    <span
      class="tw-float-right tw-italic ml-1"
      :style="{ color: getActivityTextColor(scheduleEntry) }"
    >
      {{ activityResponsiblesCommaSeparated(scheduleEntry) }}
    </span>
  </div>
</template>

<script>
import { activityResponsiblesCommaSeparated } from '@/common/helpers/activityResponsibles.js'
import { contrastColor } from '@/common/helpers/colors.js'

export default {
  props: {
    scheduleEntry: { type: Object, required: true },
  },
  methods: {
    getActivityColor(scheduleEntry) {
      return scheduleEntry.activity().category().color
    },
    getActivityTextColor(scheduleEntry) {
      const color = this.getActivityColor(scheduleEntry)
      return contrastColor(color)
    },
    activityResponsiblesCommaSeparated(scheduleEntry) {
      const responsibles = activityResponsiblesCommaSeparated(
        scheduleEntry.activity(),
        this.$t.bind(this)
      )

      if (responsibles === '') {
        return ''
      }

      return `[${responsibles}]`
    },
  },
}
</script>
