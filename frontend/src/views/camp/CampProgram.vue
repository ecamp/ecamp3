<!--
Show all activity schedule entries of a single period.
-->

<template>
  <content-card>
    <template #title>
      <period-switcher v-if="$vuetify.breakpoint.xsOnly" :period="period" />
    </template>
    <schedule-entries :period="period" :show-button="true">
      <template #default="slotProps">
        <template v-if="slotProps.loading">
          <v-skeleton-loader type="table" />
        </template>
        <template v-else>
          <picasso
            class="mx-2 ma-sm-0 pa-sm-2"
            :schedule-entries="slotProps.scheduleEntries"
            :period="period()"
            :start="Date.parse(period().start)"
            :end="Date.parse(period().end)"
            :dialog-activity-create="slotProps.showActivityCreateDialog"
            editable />
        </template>
      </template>
    </schedule-entries>
  </content-card>
</template>
<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import Picasso from '@/components/camp/picasso/Picasso.vue'
import ScheduleEntries from '@/components/scheduleEntry/ScheduleEntries.vue'
import PeriodSwitcher from '@/components/camp/PeriodSwitcher.vue'

export default {
  name: 'CampProgram',
  components: {
    PeriodSwitcher,
    ContentCard,
    Picasso,
    ScheduleEntries
  },
  props: {
    period: { type: Function, required: true }
  }
}
</script>
