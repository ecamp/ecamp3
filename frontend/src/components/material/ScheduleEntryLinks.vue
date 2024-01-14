<template>
  <v-skeleton-loader v-if="loading" type="text" />
  <router-link
    v-else-if="items.length === 1"
    :to="scheduleEntryRoute(items[0])"
    class="e-title-link tabular-nums"
  >
    <CategoryChip dense :category="activity.category()" />&thinsp;{{
      $vuetify.display.smAndUp ? `${activity.title}: ${items[0].number}` : items[0].number
    }}
  </router-link>
  <span v-else class="d-inline-flex flex-sm-wrap gap-1 align-center py-sm-1">
    <span class="mr-sm-auto">
      <CategoryChip
        class="flex-shrink-0"
        :to="scheduleEntryRoute(items[0])"
        dense
        :category="activity.category()"
      />{{ $vuetify.display.smAndUp ? `\u2009${activity.title}:` : '' }}
    </span>
    <span class="d-inline-grid d-sm-inline-flex flex-wrap gap-1"
      ><router-link
        v-for="(scheduleEntry, index) in items"
        :key="scheduleEntry.id"
        class="e-title-link tabular-nums"
        :to="scheduleEntryRoute(scheduleEntry)"
        small
        >{{
          index < items.length - 1 ? `${scheduleEntry.number},` : scheduleEntry.number
        }}</router-link
      >
    </span>
  </span>
</template>

<script>
import { scheduleEntryRoute } from '@/router.js'
import CategoryChip from '@/components/generic/CategoryChip.vue'

export default {
  name: 'ScheduleEntryLinks',
  components: { CategoryChip },
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
  },
}
</script>
