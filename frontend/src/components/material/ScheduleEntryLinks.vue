<template>
  <span>
    <v-skeleton-loader v-if="loading" type="text" />
    <span v-else>
      <span v-for="(scheduleEntry, index) in items"
            :key="scheduleEntry._meta.self">
        <router-link
          :to="scheduleEntryRoute(scheduleEntry)"
          small
          class="short-button">
          {{ getScheduleEntryCaption(scheduleEntry) }}
        </router-link>
        <span v-if="index + 1 < items.length"><br></span>
      </span>
    </span>

  </span>
</template>

<script>
import { scheduleEntryRoute } from '@/router.js'

export default {
  name: 'ScheduleEntryLinks',
  components: {
  },
  props: {
    activityPromise: { type: Promise, required: true }
  },
  data: () => {
    return {
      activity: null,
      loading: true
    }
  },
  computed: {
    items () {
      if (!this.activity) return []
      return this.activity.scheduleEntries().items
    }
  },
  async mounted () {
    this.activity = await this.activityPromise
    await this.activity.scheduleEntries().$loadItems()

    this.loading = false
  },
  methods: {
    scheduleEntryRoute,
    getScheduleEntryCaption (scheduleEntry) {
      const number = scheduleEntry.number
      const title = scheduleEntry.activity().title

      if (this.$vuetify.breakpoint.smAndUp) {
        if (title.length > 13) {
          return number + ': ' + title.substr(0, 13) + '...'
        } else {
          return number + ': ' + title
        }
      } else {
        return number
      }
    }
  }
}
</script>
