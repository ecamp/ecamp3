<template>
  <v-skeleton-loader v-if="loading" type="text" />
  <span v-else>
    <template v-for="(scheduleEntry, index) in items">
      <router-link
        :key="scheduleEntry._meta.self"
        :to="scheduleEntryRoute(scheduleEntry)"
        small
      >
        <Truncate v-if="$vuetify.breakpoint.smAndUp" style="max-width: 20vw">
          <CategoryChip
            v-if="items.length === 1"
            dense
            :schedule-entry="scheduleEntry"
          />&thinsp;<span class="e-title-link">{{
            getScheduleEntryCaption(scheduleEntry)
          }}</span>
        </Truncate>
        <span v-else style="white-space: nowrap"
          ><CategoryChip
            v-if="items.length === 1"
            dense
            :schedule-entry="scheduleEntry"
          />&nbsp;<span class="e-title-link">{{
            getScheduleEntryCaption(scheduleEntry)
          }}</span></span
        >
      </router-link>
      <br v-if="index + 1 < items.length" :key="scheduleEntry._meta.self" />
    </template>
  </span>
</template>

<script>
import { scheduleEntryRoute } from '@/router.js'
import Truncate from '@/components/generic/Truncate.vue'
import CategoryChip from '@/components/generic/CategoryChip.vue'

export default {
  name: 'ScheduleEntryLinks',
  components: { CategoryChip, Truncate },
  props: {
    activityPromise: { type: Promise, required: true },
  },
  data: () => {
    return {
      activity: null,
      loading: true,
    }
  },
  computed: {
    items() {
      if (!this.activity) return []
      return this.activity.scheduleEntries().items
    },
  },
  async mounted() {
    this.activity = await this.activityPromise
    await this.activity.scheduleEntries().$loadItems()

    this.loading = false
  },
  methods: {
    scheduleEntryRoute,
    getScheduleEntryCaption(scheduleEntry) {
      const number = scheduleEntry.number
      const title = scheduleEntry.activity().title

      if (this.$vuetify.breakpoint.smAndUp) {
        return `${number}: ${title}`
      } else {
        return `${number}`
      }
    },
  },
}
</script>
