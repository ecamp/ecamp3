<template>
  <ContentNodeCard class="ec-ys-physical-activity-duration" v-bind="$props">
    <p v-if="!activity" class="text-medium-emphasis mb-0">
      {{ $t('contentNode.ysPhysicalActivityDuration.templateHint') }}
    </p>
    <template v-else>
      <api-select
        v-for="scheduleEntry in scheduleEntries"
        :key="scheduleEntry._meta.self"
        :uri="scheduleEntry._meta.self"
        path="ysPhysicalActivityDurationMinutes"
        :label="scheduleEntryLabel(scheduleEntry)"
        :items="items"
        :placeholder="$t('contentNode.ysPhysicalActivityDuration.none')"
        persistent-placeholder
        density="compact"
        :disabled="layoutMode || disabled"
      />
    </template>
  </ContentNodeCard>
</template>

<script>
import ContentNodeCard from '@/components/activity/content/layout/ContentNodeCard.vue'
import { contentNodeMixin } from '@/mixins/contentNodeMixin.js'
import { dateHelperUTCFormatted } from '@/mixins/dateHelperUTCFormatted.js'
import {
  ysPhysicalActivityDuration,
  ysPhysicalActivityDurationOptions,
} from '@/common/helpers/ysPhysicalActivityDuration.js'

export default {
  name: 'YSPhysicalActivityDuration',
  components: { ContentNodeCard },
  mixins: [contentNodeMixin, dateHelperUTCFormatted],
  inject: {
    injectedActivity: { from: 'activity', default: null },
  },
  computed: {
    activity() {
      return this.injectedActivity?.() ?? null
    },
    scheduleEntries() {
      return this.activity.scheduleEntries().items
    },
    items() {
      return [
        { value: null, text: this.$t('contentNode.ysPhysicalActivityDuration.none') },
        ...ysPhysicalActivityDurationOptions.map((minutes) => ({
          value: minutes,
          text: ysPhysicalActivityDuration(minutes, this.$t.bind(this)),
        })),
      ]
    },
  },
  methods: {
    scheduleEntryLabel(scheduleEntry) {
      return [
        scheduleEntry.number,
        this.rangeShort(scheduleEntry.start, scheduleEntry.end),
      ]
        .filter(Boolean)
        .join(' ')
    },
  },
}
</script>
