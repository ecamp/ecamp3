<template>
  <div
    class="e-physical-activity-duration"
    :class="{ 'e-physical-activity-duration--visible': visible }"
  >
    <span
      class="e-physical-activity-duration__line"
      :style="{ height: `${visible ? fraction * 100 : 0}%` }"
    />
    <span
      class="e-physical-activity-duration__text"
      :aria-hidden="!visible"
      :title="$t('entity.scheduleEntry.fields.ysPhysicalActivityDuration')"
    >
      {{ duration }}
    </span>
  </div>
</template>

<script>
import { ONE_MINUTE_IN_MILLISECONDS } from '@/helpers/vCalendarDragAndDrop.js'
import { isYsPhysicalActivityDurationOpenEnded } from '@/common/helpers/ysPhysicalActivityDuration.js'

export default {
  props: {
    duration: { type: String, required: true },
    minutes: { type: Number, required: true },
    entryDuration: { type: Number, required: true },
    visible: { type: Boolean, default: false },
  },
  computed: {
    fraction() {
      if (this.entryDuration <= 0) return 0
      if (isYsPhysicalActivityDurationOpenEnded(this.minutes)) return 1
      return Math.min(
        1,
        Math.max(0, (this.minutes * ONE_MINUTE_IN_MILLISECONDS) / this.entryDuration)
      )
    },
  },
}
</script>

<style scoped>
.e-physical-activity-duration {
  position: absolute;
  inset: 0;
  pointer-events: none;
}

.e-physical-activity-duration__line {
  position: absolute;
  left: 0;
  bottom: 0;
  width: 100%;
  background: repeating-linear-gradient(-45deg, transparent 0 4px, currentColor 4px 8px);
  opacity: 0.15;
  transition: height 0.35s ease;
}

.e-physical-activity-duration__text {
  position: absolute;
  bottom: 0;
  left: 3px;
  font-size: 11px;
  line-height: 1.4;
  white-space: nowrap;
  visibility: hidden;
  opacity: 0;
  transition:
    opacity 0.35s ease,
    visibility 0.35s ease;
}

.e-physical-activity-duration--visible .e-physical-activity-duration__text {
  pointer-events: auto;
  visibility: visible;
  opacity: 1;
}
</style>
